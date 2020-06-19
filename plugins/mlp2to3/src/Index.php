<?php

namespace Inpsyde\MultilingualPress2to3;


use ArrayIterator;
use Countable;
use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\ContainerHasCapableTrait;
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
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Exception as RootException;
use Iterator;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Throwable;
use Traversable;

/**
 * Given a list of data objects, allows access to them by key.
 *
 * @package MultilingualPress2to3
 */
class Index implements ContainerInterface, IteratorInterface, Countable
{
    use IteratorTrait;

    use TrackingIteratorTrait;

    use IteratorIteratorTrait;

    use IterationAwareTrait;

    use ContainerHasCapableTrait;

    use ContainerGetCapableTrait;

    use NormalizeStringCapableTrait;

    use StringTranslatingTrait;

    use NormalizeKeyCapableTrait;

    use NormalizeIterableCapableTrait;

    use CreateIterationCapableTrait;

    use CreateInvalidArgumentExceptionCapableTrait;

    use CreateContainerExceptionCapableTrait;

    use CreateNotFoundExceptionCapableTrait;

    use CreateIteratorExceptionCapableTrait;

    protected $data;

    /**
     * @var string
     */
    protected $keyRetriever;

    /**
     * @var object
     */
    protected $index;

    /**
     * @var Iterator
     */
    protected $iterationTracker;

    /**
     * @param mixed[]|object|Traversable $data A list of entries to index.
     * @param callable $keyRetriever A procedure that retrieves the key to index by from each entry.
     */
    public function __construct($data, callable $keyRetriever)
    {
        $this->data = $data;
        $this->keyRetriever = $keyRetriever;
    }

    /**
     * Retrieves indexed data.
     *
     * If data has not been indexed yet, indexes it and caches the result.
     *
     * @return object The index.
     *
     * @throws Throwable If problem indexing.
     */
    protected function _ensureIndex()
    {
        $data = $this->data;

        if ($this->index === null) {
            $this->index = $this->_index($data);
        }

        return $this->index;
    }

    /**
     * Indexes the given data.
     *
     * @param mixed|object|Traversable $items The list of items to index.
     *
     * @return array A map of item IDs to their values.
     *
     * @throws Throwable If problem indexing.
     */
    protected function _index($items): array
    {
        $index = [];
        foreach ($items as $element) {
            $key = $this->_getItemId($element);

            if (is_null($key)) {
                continue;
            }

            $index[$key] = $element;
        }

        return $index;
    }

    /**
     * Retrieves a value that uniquely identifies the given item.
     *
     * @param mixed $item The item to get the ID for.
     *
     * @return string|null The ID value, or null if ID not found.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function _getItemId($item)
    {
        $retriever = $this->keyRetriever;
        if (!is_callable($retriever)) {
            throw new RuntimeException($this->__('Retriever procedure must be invocable'));
        }

        $key = $retriever($item);

        return $key;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $this->_ensureIndex();

        return count($this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $this->_ensureIndex();

        return $this->_containerGet($this->index, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        $this->_ensureIndex();

        return $this->_containerHas($this->index, $id);
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
        $this->_rewind();
    }

    /**
     * {@inheritdoc}
     */
    protected function _getTracker()
    {
        if ($this->iterationTracker === null) {
            $this->iterationTracker = new ArrayIterator($this->index);
        }

        return $this->iterationTracker;
    }

    /**
     * {@inheritdoc}
     */
    protected function _throwIteratorException($message = null, $code = null, RootException $previous = null)
    {
        throw $this->_createIteratorException($message, $code, $previous, $this);
    }
}