<?php

namespace Dhii\Collection;

abstract class AbstractBaseCountableMap extends AbstractBaseMap implements
    /* @since [*next-version*] */
    CountableMapInterface
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function count()
    {
        return $this->_getDataStore()->count();
    }
}
