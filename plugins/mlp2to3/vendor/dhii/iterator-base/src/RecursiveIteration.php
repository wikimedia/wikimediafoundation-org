<?php

namespace Dhii\Iterator;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use stdClass;
use Traversable;

/**
 * Represents an iteration in a recursive iterator.
 *
 * @since [*next-version*]
 */
class RecursiveIteration extends AbstractBaseIteration implements RecursiveIterationInterface
{
    /* Awareness of path segments.
     *
     * @since [*next-version*]
     */
    use PathSegmentsAwareTrait;

    /* Ability to calculate depth from segments.
     *
     * @since [*next-version*]
     */
    use GetDepthCapableTrait;

    /* Ability to normalize iterables.
     *
     * @since [*next-version*]
     */
    use NormalizeIterableCapableTrait;

    /* Ability to normalize integers.
     *
     * @since [*next-version*]
     */
    use NormalizeIntCapableTrait;

    /* Ability to normalize strings.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /* Ability to count elements in an iterable.
     *
     * @since [*next-version*]
     */
    use CountIterableCapableTrait;

    /* Ability to resolve iterators.
     *
     * @since [*next-version*]
     */
    use ResolveIteratorCapableTrait;

    /* Factory of Invalid Argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* Factory of Out of Range exceptions.
     *
     * @since [*next-version*]
     */
    use CreateOutOfRangeExceptionCapableTrait;

    /* Ability to translate strings.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|int|null                            $key   The iteration key.
     * @param mixed                                      $value The iteration value.
     * @param string[]|Stringable[]|Traversable|stdClass $path  A list of path segments.
     *
     * @throws InvalidArgumentException If the key, or the path segment list, is invalid.
     */
    public function __construct($key, $value, $path = [])
    {
        $this->_setKey($key);
        $this->_setValue($value);
        $this->_setPathSegments($path);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getPathSegments()
    {
        return $this->_getPathSegments();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getDepth()
    {
        return $this->_getDepth();
    }
}
