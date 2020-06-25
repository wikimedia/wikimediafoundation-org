<?php

namespace Dhii\Iterator;

use Dhii\Data\Hierarchy\ChildrenAwareInterface;

/**
 * Common functionality for recursive iterators that iterate over children-aware items.
 *
 * @since [*next-version*]
 */
trait ChildrenAwareRecursiveIteratorTrait
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _isElementHasChildren($value)
    {
        return ($value instanceof ChildrenAwareInterface) && $value->hasChildren();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function &_getElementChildren($value)
    {
        $children = $this->_isElementHasChildren($value)
            /* @var $value ChildrenAwareInterface */
            ? $value->getChildren()
            : [];

        return $children;
    }
}
