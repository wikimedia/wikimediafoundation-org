<?php

namespace Dhii\Iterator\Exception;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Iterator\IterationAwareTrait;
use Dhii\Iterator\IterationInterface;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface;
use Exception as RootException;

/**
 * An exception that occurs in relation to an iteration.
 *
 * @since [*next-version*]
 */
class IterationException extends IteratingException implements IterationExceptionInterface
{
    /*
     * Provides awareness of an iteration instance and storage functionality.
     *
     * @since [*next-version*]
     */
    use IterationAwareTrait {
        _getIteration as public getIteration;
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
     * @param string|StringableInterface|null $message   The error message, if any.
     * @param int|null                        $code      The error code, if any.
     * @param RootException|null              $previous  The previous exception, if any.
     * @param IterationInterface|null         $iteration The
     */
    public function __construct(
        $message = null,
        $code = null,
        RootException $previous = null,
        IterationInterface $iteration = null
    ) {
        $message = ($message === null)
            ? ''
            : $this->_normalizeString($message);

        $code = ($code === null)
            ? 0
            : $this->_normalizeInt($code);

        $this->_setIteration($iteration);

        parent::__construct($message, $code, $previous);
    }
}
