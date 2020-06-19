<?php declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3;

use ArrayAccess;
use ArrayIterator;
use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\ContainerSetCapableTrait;
use Dhii\Data\Container\ContainerUnsetCapableTrait;
use Dhii\Data\Container\CreateContainerExceptionCapableTrait;
use Dhii\Data\Container\CreateNotFoundExceptionCapableTrait;
use Dhii\Data\Object\NormalizeKeyCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Iterator\CreateIterationCapableTrait;
use Dhii\Iterator\CreateIteratorExceptionCapableTrait;
use Dhii\Iterator\IterationAwareTrait;
use Dhii\Iterator\IteratorInterface;
use Dhii\Iterator\IteratorIteratorTrait;
use Dhii\Iterator\IteratorTrait;
use Dhii\Iterator\TrackingIteratorTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use DomainException;
use Exception as RootException;
use InvalidArgumentException;
use Iterator;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use stdClass;
use Throwable;

/**
 * An object that provides multiple interfaces to access data from a JSON string.
 *
 * This is useful, because the string is not being accessed or decoded until
 * this class's interface is consumed. Works well with stringable wrappers.
 *
 * @package MultilingualPress2to3
 */
class Json implements
    ArrayAccess,
    BaseContainerInterface,
    IteratorInterface,
    Stringable
{
    use IteratorTrait;

    use TrackingIteratorTrait;

    use IteratorIteratorTrait;

    use IterationAwareTrait;

    use StringTranslatingTrait;

    use ContainerGetCapableTrait;

    use ContainerHasCapableTrait;

    use ContainerSetCapableTrait;

    use ContainerUnsetCapableTrait;

    use NormalizeKeyCapableTrait;

    use NormalizeStringCapableTrait;

    use NormalizeStringableCapableTrait;

    use NormalizeIterableCapableTrait;

    use CreateIterationCapableTrait;

    use CreateNotFoundExceptionCapableTrait;

    use CreateInvalidArgumentExceptionCapableTrait;

    use CreateContainerExceptionCapableTrait;

    use CreateIteratorExceptionCapableTrait;

    /**
     * @var Stringable|string
     */
    protected $string;

    /**
     * @var stdClass
     */
    protected $object;

    /**
     * @var Iterator
     */
    protected $iterator;
    /**
     * @var bool
     */
    protected $isDebug;

    /**
     * @param string|Stringable|object|array|mixed $json The JSON string, object, list, or value.
     * Stringable objects will be treated as strings.
     */
    public function __construct($json, bool $isDebug)
    {
        try {
            $this->string = $this->_normalizeStringable($json);
        } catch (InvalidArgumentException $e) {
            if (!(is_object($json) || is_array($json) || is_scalar($json) || is_null($json))){
                throw new InvalidArgumentException($this->__('The JSON must be a string, a stringable object, or a valid JSON value: map, list, scalar, null'));
            }

            $this->object = $json;
        }

        $this->isDebug = $isDebug;
    }

    /**
     * Retrieves a value for the specified key.
     *
     * @param string|int $key The key to retrieve the value for.
     *
     * @return mixed The key's value.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getKey($key)
    {
        $this->_ensureDecoded();

        return $this->_containerGet($this->object, $key);
    }

    /**
     * Determines whether a value exists for a specified key.
     *
     * @param string|int $key The key to check for.
     *
     * @return bool True if a value for the specified key exists; false otherwise.
     *
     * @throws Throwable If problem checking.
     */
    protected function _hasKey($key): bool
    {
        $this->_ensureDecoded();

        return $this->_containerHas($this->object, $key);
    }

    /**
     * Assigns the specified value to the specified key.
     *
     * @param string $key The key to set the value for.
     * @param mixed $value The value to set for the specified key.
     *
     * @throws Throwable If problem assigning.
     */
    protected function _setKey(string $key, $value)
    {
        $this->_ensureDecoded();

        $this->_containerSet($this->object, $key, $value);
        $this->_setString(null);
    }

    /**
     * Removes the specified key.
     *
     * @param string|int $key The key to remove.
     *
     * @throws Throwable If problem removing.
     */
    protected function _unsetKey($key)
    {
        $this->_ensureDecoded();

        $this->_containerUnset($this->object, $key);
        $this->_setString(null);
    }

    /**
     * Assigns this object's string representation.
     *
     * @param string|null $string The JSON string to set for this object.
     *
     * @throws Throwable If problem assigning.
     */
    protected function _setString($string)
    {
        if (!is_string($string) && !is_null($string)) {
            throw $this->_createInvalidArgumentException($this->__('String must be a valid string or null'), null, null, $string);
        }

        $this->string = $string;
    }

    /**
     * Decodes this object's string representation and caches the result, if not already decoded.*
     *
     * @throws Throwable If problem decoding.
     */
    protected function _ensureDecoded()
    {
        if ($this->object === null) {
            $this->object = $this->_decodeJson($this->string);
        }
    }

    /**
     * Decodes the specified JSON string.
     *
     * @param string|Stringable $json The JSON string to decode.
     *
     * @return array|mixed|object The result of decoding.
     *
     * @throws Throwable If problem decoding.
     */
    protected function _decodeJson($json)
    {
        $json = (string) $json;
        $result = json_decode($json);
        $error = json_last_error();

        if($result === null && $error !== JSON_ERROR_NONE) {
            throw new DomainException(json_last_error_msg());
        }

        return $result;
    }

    /**
     * Retrieves the string representation of this object's data.
     *
     * @return string The string representation.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getString(): string
    {
        if ($this->string === null) {
            $this->string = json_encode($this->object);
        }

        $string = $this->_normalizeString($this->string);

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        try {
            return $this->_getString();
        } catch (Throwable $e) {
            return $this->isDebug
                ? (string) $e
                : '';
        }
    }

    public function __get($name)
    {
        return $this->_getKey($name);
    }

    public function __set($name, $value)
    {
        $this->_setKey($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->_getKey($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->_hasKey($id);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->_hasKey($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->_getKey($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->_setKey($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->_unsetKey($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->_value();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->_key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->_valid();
    }

    /**
     * {@inheritdoc}
     */
    public function getIteration()
    {
        return $this->_getIteration();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->_next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator = null;
        $this->_rewind();
    }

    /**
     * {@inheritdoc}
     */
    protected function _getTracker()
    {
        if (!($this->iterator instanceof Iterator)) {
            $this->_ensureDecoded();
            $object = $this->_normalizeIterable($this->object);
            $this->iterator = new ArrayIterator($object);
        }

        return $this->iterator;
    }

    /**
     * {@inheritdoc}
     */
    protected function _throwIteratorException($message = null, $code = null, RootException $previous = null)
    {
        throw $this->_createIteratorException($message, $code, $previous, $this);
    }
}