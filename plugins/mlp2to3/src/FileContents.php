<?php

namespace Inpsyde\MultilingualPress2to3;

use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\String\StringableInterface;
use RuntimeException;
use Throwable;

/**
 * Allows automatic deferred retrieval of file contents after creation time.
 *
 * Useful if you work with a stringable value, but want to delay
 * filesystem access until the its contents are actually used.
 *
 * @package MultilingualPress2to3
 */
class FileContents implements StringableInterface
{
    use StringTranslatingTrait;

    /**
     * @var string
     */
    protected $filePath;
    /**
     * @var bool
     */
    protected $isDebug;


    /**
     * @param string $filePath Absolute path to the file, the contents of which are represented by this instance.
     * @param bool $isDebug If true, the `__toString()` method will return error text in case there's a problem reading.
     */
    public function __construct(string $filePath, bool $isDebug)
    {
        $this->filePath = $filePath;
        $this->isDebug = $isDebug;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        try {
            $contents = $this->_getContents();

            return $contents;
        } catch (Throwable $e) {
            $contents = $this->isDebug
                ? (string) $e
                : '';

            return $contents;
        }
    }

    /**
     * Retrieve contents of the file at the configured path.
     *
     * @return string The file contents.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getContents()
    {
        $path = $this->filePath;
        $contents = $this->_getFileContents($path);

        return $contents;
    }

    /**
     * Retrieves contents of the file at the specified path.
     *
     * @return string The contents of a file.
     *
     * @throws RuntimeException If unable to read from file.
     * @throws Throwable If problem retrieving.
     */
    protected function _getFileContents(string $path): string
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException($this->__('Failed reading file at path "%1$s"', $path));
        }

        return $contents;
    }
}