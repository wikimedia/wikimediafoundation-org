<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Attachment;

use stdClass;

/**
 * Class Collection
 * @package Inpsyde\MultilingualPress\Attachment
 */
class Collection
{
    const DEFAULT_LIMIT = 0;
    const DEFAULT_OFFSET = 0;
    const META_KEY_ATTACHMENTS = '_wp_attachment_metadata';

    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * Attachments constructor.
     * @param \wpdb $wpdb
     */
    public function __construct(\wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * Extracts all registered attachment paths from the database as an array with directories
     * relative to uploads as keys, and arrays of file paths as values.
     *
     * Only files referenced in the database are trustworthy, and will therefore get copied.
     *
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function list(
        int $offset = self::DEFAULT_OFFSET,
        int $limit = self::DEFAULT_LIMIT
    ): array {

        $sql = "SELECT post_id, meta_value FROM {$this->wpdb->postmeta} WHERE meta_key = %s LIMIT %d OFFSET %d";

        /** @var stdClass[] $metadata */
        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $metadata = $this->wpdb->get_results(
            $this->wpdb->prepare(
                $sql,
                self::META_KEY_ATTACHMENTS,
                $limit,
                $offset
            )
        );
        // phpcs:enable

        if (!$metadata) {
            return [];
        }

        $paths = [];
        foreach ($metadata as $metadataValue) {
            list($dir, $files) = $this->filesPaths($metadataValue);
            ($dir && $files) and $paths[] = compact('dir', 'files');
        }

        return $paths;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->wpdb->postmeta} WHERE meta_key = %s";
        /** @var stdClass[] $metadata */
        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $count = (int)$this->wpdb->get_var(
            $this->wpdb->prepare($sql, self::META_KEY_ATTACHMENTS)
        );
        // phpcs:enable

        return (int)ceil($count);
    }

    /**
     * @param stdClass $metadata
     * @return array
     */
    private function filesPaths(stdClass $metadata): array
    {
        $metaValue = maybe_unserialize($metadata->meta_value);

        $file = \is_array($metaValue) ? ($metaValue['file'] ?? '') : '';
        $dir = $file ? \dirname($file) : null;
        if (!$dir) {
            return [null, null];
        }

        $sizes = $metaValue['sizes'] ?? [];

        $files = $sizes && \is_array($sizes) ? array_column($sizes, 'file') : [];
        array_unshift($files, basename($file));
        array_unshift($files, basename($this->backupFile((int)$metadata->post_id)));

        return [$dir, array_filter($files)];
    }

    /**
     * Get the Attachment backup file.
     *
     * When the image is edited in WordPress media editor, the original image will be backed up and
     * stored in _wp_attachment_backup_sizes meta, so the users can restore the original image later.
     * When copying the site attachments to a new site with MLP and "Based On Site" option we also need to check the
     * backup files and copy them as well, so in a new site the users will be also able to restore the original images.
     *
     * @param int $postId The attachment post ID
     * @return string The backed up file.
     */
    protected function backupFile(int $postId): string
    {
        $backupFileMeta = maybe_unserialize(get_post_meta($postId, '_wp_attachment_backup_sizes', true));

        return !empty($backupFileMeta['full-orig']) && !empty($backupFileMeta['full-orig']['file'])
            ? $backupFileMeta['full-orig']['file']
            : '';
    }
}
