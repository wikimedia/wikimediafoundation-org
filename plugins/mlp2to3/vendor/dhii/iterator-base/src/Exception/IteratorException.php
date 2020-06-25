<?php

namespace Dhii\Iterator\Exception;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Iterator\IteratorAwareTrait;
use Dhii\Iterator\IteratorInterface;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface;
use Exception as RootException;

/**
 * An exception that occurs in relation to an iterator.
 *
 * @since [*next-version*]
 */
class IteratorException extends IteratingException implements IteratorExceptionInterface
{
    /*
     * Provides awareness of an iteration instance and storage functionality.
     *
     * @since [*next-version*]
     */
    use IteratorAwareTrait {
        _getIterator as public getIterator;
    }

    /*
     * Normalize string normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /*
     * Provides integer normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeIntCapableTrait;

    /*
     * Provides functionality for creating invalid argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /*
     * Provides string translation functionality.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|StringableInterface|null $message  The error message, if any.
     * @param int|null                        $code     The error code, if any.
     * @param RootException|null              $previous The previous exception, if any.
     * @param IteratorInterface|null          $iterator The iterator instance, if any.
     */
    public function __construct(
        $message = null,
        $code = null,
        RootException $previous = null,
        IteratorInterface $iterator = null
    ) {
        $message = ($message === null)
            ? ''
            : $this->_normalizeString($message);

        $code = ($code === null)
            ? 0
            : $this->_normalizeInt($code);

        $this->_setIterator($iterator);

        parent::__construct($message, $code, $previous);
    }
}
