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

namespace Inpsyde\MultilingualPress\Framework;

/**
 * Class Filesystem
 * @package Inpsyde\MultilingualPress\Framework
 */
class Filesystem
{
    const ACTION_INIT_CREDENTIALS = 'multilingualpress.init_filesystem_credentials';
    const FILTER_CREDENTIALS_CONTEXT = 'multilingualpress.filesystem_context';

    /**
     * @var \WP_Filesystem_Base
     */
    private $wpFilesystem;

    /**
     * @return string
     */
    public static function forceDirect(): string
    {
        return (\defined('FS_METHOD') && \is_string(FS_METHOD)) ? FS_METHOD : 'direct';
    }

    /**
     * @return bool
     */
    public static function forceCredentials(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    public static function removeForceFilters()
    {
        remove_filter('filesystem_method', [__CLASS__, 'forceDirect'], 2);
        remove_filter('request_filesystem_credentials', [__CLASS__, 'forceCredentials'], 2);
    }

    /*
     * We are not really interested in methods that requires credentials like FTP and such,
     * so we don't support them out-of-the-box, but following filters are trivial to remove.
     *
     * @return void
     */
    private static function addForceFilters()
    {
        add_filter('filesystem_method', [__CLASS__, 'forceDirect'], 2);
        add_filter('request_filesystem_credentials', [__CLASS__, 'forceCredentials'], 2);
    }

    /**
     * @param string $source
     * @param string $destination
     * @param int $mode
     *
     * @return bool
     */
    public function copy(string $source, string $destination, int $mode = null): bool
    {
        if (!$source || !$destination) {
            return false;
        }

        return (bool)$this->wpFilesystem()
            ->copy($source, $destination, true, $mode ?? $this->fileChmod());
    }

    /**
     * @param string $source
     * @param string $destination
     * @param int $mode
     *
     * @return bool
     */
    public function copyIfNotExist(string $source, string $destination, int $mode = null): bool
    {
        if (!$source || !$destination) {
            return false;
        }

        return (bool)$this->wpFilesystem()
            ->copy($source, $destination, false, $mode ?? $this->fileChmod());
    }

    /**
     * @param string $source
     * @param string $destination
     *
     * @return bool
     */
    public function move(string $source, string $destination): bool
    {
        if (!$source || !$destination) {
            return false;
        }

        return (bool)$this->wpFilesystem()->move($source, $destination, true);
    }

    /**
     * @param string $source
     * @param string $destination
     *
     * @return bool
     */
    public function moveIfNotExist(string $source, string $destination): bool
    {
        if (!$source || !$destination) {
            return false;
        }

        return (bool)$this->wpFilesystem()->move($source, $destination, false);
    }

    /**
     * @param string $filepath
     *
     * @return bool
     */
    public function deleteFile(string $filepath): bool
    {
        if (!$filepath) {
            return false;
        }

        $exist = $this->pathExists($filepath);
        if (!$exist) {
            return true;
        }

        if ($exist && !$this->isFile($filepath)) {
            return false;
        }

        return (bool)$this->wpFilesystem()->delete($filepath, false, 'f');
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function deleteFolder(string $path): bool
    {
        if (!$path) {
            return false;
        }

        $exist = $this->pathExists($path);
        if (!$exist) {
            return true;
        }

        if ($exist && !$this->isDir($path)) {
            return false;
        }

        return (bool)$this->wpFilesystem()->delete($path, true, 'd');
    }

    /**
     * @param string $filepath
     *
     * @return bool
     */
    public function pathExists(string $filepath): bool
    {
        return $filepath && $this->wpFilesystem()->exists($filepath);
    }

    /**
     * @param string $filepath
     *
     * @return bool
     */
    public function isFile(string $filepath): bool
    {
        return $filepath && $this->wpFilesystem()->is_file($filepath);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isDir(string $path): bool
    {
        return $path && $this->wpFilesystem()->is_dir($path);
    }

    /**
     * @param string $filepath
     *
     * @return bool
     */
    public function isReadable(string $filepath): bool
    {
        return $filepath && $this->wpFilesystem()->is_readable($filepath);
    }

    /**
     * @param string $path
     * @param int $mode
     *
     * @return bool
     */
    public function mkDirP(string $path, int $mode = null): bool
    {
        $prefix = '';
        if (wp_is_stream($path)) {
            list($prefix, $path) = explode('://', $path, 2);
            $prefix .= '://';
        }

        $path = preg_replace('|(?<=.)/+|', '/', str_replace('\\', '/', $path));
        if (':' === ($path[1] ?? null)) {
            $prefix .= "{$path[0]}:/";
            $path = substr($path, 3);
        }

        if ('/' === ($path[0] ?? null)) {
            $prefix .= '/';
            $path = ltrim($path, '/');
        }

        $path = rtrim($path, '/');
        if (!$path) {
            return false;
        }

        // Find closest existent parent directory.
        $basePath = \dirname($prefix . $path);
        while (!$this->isDir($basePath) && \dirname($basePath) !== $basePath) {
            $basePath = \dirname($basePath);
        }

        if (!$this->wpFilesystem()->is_writable($basePath)) {
            return false;
        }

        if ($mode === null) {
            $stat = @stat($basePath);
            $mode = $stat ? ($stat['mode'] & 0007777) : $this->folderChmod();
        }

        $chunks = array_filter(explode('/', substr($prefix . $path, strlen($basePath) + 1)));

        $built = $basePath;
        foreach ($chunks as $chunk) {
            $target = $built ? "{$built}/{$chunk}" : $chunk;
            if (!$this->mkDir($target, $mode)) {
                return false;
            }

            $built = $target;
        }

        return true;
    }

    /**
     * @param string $path
     * @param int $mode
     *
     * @return bool
     */
    public function mkDir(string $path, int $mode = null): bool
    {
        if (!$path) {
            return false;
        }

        $filesystem = $this->wpFilesystem();

        return $filesystem->is_dir($path)
            || $filesystem->mkdir($path, $mode ?? $this->folderChmod());
    }

    /**
     * Return an instance of WP_Filesystem.
     *
     * @return \WP_Filesystem_Base
     */
    private function wpFilesystem(): \WP_Filesystem_Base
    {
        if ($this->wpFilesystem) {
            return $this->wpFilesystem;
        }

        global $wp_filesystem;
        $success = true;

        if (!$this->isFilesystemBase($wp_filesystem)) {
            require_once ABSPATH . '/wp-admin/includes/file.php';

            self::addForceFilters();

            /**
             * Fired before request_filesystem_credentials is called.
             *
             * Useful to remove "force" filters above and enable credentials form.
             * `Filesystem::removeForceFilters()` method can be used for the scope.
             */
            do_action(self::ACTION_INIT_CREDENTIALS);

            $context = apply_filters(self::FILTER_CREDENTIALS_CONTEXT, false);
            $params = request_filesystem_credentials('', '', false, $context, null);

            static::removeForceFilters();

            $success = WP_Filesystem($params, $context);
        }

        if (!$success || !$this->isFilesystemBase($wp_filesystem)) {
            $this->wpFilesystem = new \WP_Filesystem_Base();

            return $this->wpFilesystem;
        }

        $this->wpFilesystem = $wp_filesystem;

        return $wp_filesystem;
    }

    /**
     * @return int
     */
    private function folderChmod(): int
    {
        if (\defined('FS_CHMOD_DIR') && \is_int(FS_CHMOD_DIR)) {
            return FS_CHMOD_DIR;
        }

        static $mode;
        if ($mode) {
            return $mode;
        }

        $mode = fileperms(ABSPATH) & 0777 | 0755;

        return $mode;
    }

    /**
     * @return int
     */
    private function fileChmod(): int
    {
        if (\defined('FS_CHMOD_FILE') && \is_int(FS_CHMOD_FILE)) {
            return FS_CHMOD_FILE;
        }

        static $mode;
        if ($mode) {
            return $mode;
        }

        $mode = fileperms(ABSPATH . 'index.php') & 0777 | 0644;

        return $mode;
    }

    /**
     * Check a thing against \WP_Filesystem_Base
     *
     * @param mixed $wpFilesystem
     * @return bool
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    private function isFilesystemBase($wpFilesystem): bool
    {
        // phpcs:enable

        return $wpFilesystem instanceof \WP_Filesystem_Base;
    }
}
