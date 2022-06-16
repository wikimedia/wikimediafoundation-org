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

use Inpsyde\MultilingualPress\Framework\BasePathAdapter;
use Inpsyde\MultilingualPress\Framework\Filesystem;
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;

/**
 * Class Duplicator
 * @package Inpsyde\MultilingualPress\Attachment
 */
class Duplicator
{
    use SwitchSiteTrait;

    const FILTER_ATTACHMENTS_PATHS = 'multilingualpress.attachments_to_target_paths';

    /**
     * @var BasePathAdapter
     */
    private $basePathAdapter;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param BasePathAdapter $basePathAdapter
     * @param Filesystem $filesystem
     */
    public function __construct(BasePathAdapter $basePathAdapter, Filesystem $filesystem)
    {
        $this->basePathAdapter = $basePathAdapter;
        $this->filesystem = $filesystem;
    }

    /**
     * Copies all attachment files of the site with given ID to the current site.
     *
     * @param int $sourceSiteId
     * @param int $targetSiteId
     * @param array $attachmentsPaths
     * @return bool
     */
    public function duplicateAttachmentsFromSite(
        int $sourceSiteId,
        int $targetSiteId,
        array $attachmentsPaths
    ): bool {

        $sourceDir = $this->basePathAdapter->basedirForSite($sourceSiteId);
        $sourceDir = trailingslashit($sourceDir);

        $previousSiteId = $this->maybeSwitchSite($targetSiteId);
        $destinationDir = trailingslashit($this->basePathAdapter->basedir());
        $attachmentsPaths = apply_filters(
            self::FILTER_ATTACHMENTS_PATHS,
            $attachmentsPaths,
            $sourceSiteId,
            $sourceDir,
            $destinationDir
        );

        $validAttachmentsPaths = $attachmentsPaths && \is_array($attachmentsPaths);
        $validSourceDir = ($this->filesystem->isDir($sourceDir)
            && $this->filesystem->isReadable($sourceDir));
        if (!$validAttachmentsPaths || !$validSourceDir) {
            return false;
        }

        $dirCopied = 0;
        foreach ($attachmentsPaths as $dir => $attachmentFiles) {
            if (\is_string($dir) && \is_array($attachmentFiles)) {
                $filesCopied = $this->copyDir(
                    $sourceDir . $dir,
                    $attachmentFiles,
                    $destinationDir . $dir
                );

                $filesCopied and ++$dirCopied;
            }
        }

        $allDirsCopied = ($dirCopied === count($attachmentsPaths));

        $this->maybeRestoreSite($previousSiteId);

        return $allDirsCopied;
    }

    /**
     * Copies all given files from one site to another.
     *
     * @param string $sourceDir
     * @param array $filepaths
     * @param string $destinationDir
     * @return bool
     */
    private function copyDir(string $sourceDir, array $filepaths, string $destinationDir): bool
    {
        if (
            !$this->filesystem->isDir($sourceDir)
            || !$this->filesystem->mkDirP($destinationDir)
        ) {
            return false;
        }

        $countPaths = 0;
        foreach ($filepaths as $filepath) {
            $source = trailingslashit($sourceDir) . $filepath;
            $destination = trailingslashit($destinationDir) . $filepath;

            // Count as it were already copied.
            if ($this->filesystem->pathExists($destination)) {
                ++$countPaths;
                continue;
            }
            if (
                $this->filesystem->pathExists($source)
                && $this->filesystem->copy($source, $destination)
            ) {
                ++$countPaths;
            }
        }

        return ($countPaths === count($filepaths));
    }
}
