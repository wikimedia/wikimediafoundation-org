<?php

namespace Dhii\Collection;

use ArrayObject;
use Dhii\Data\Object\CreateDataStoreCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use stdClass;

/**
 * An iterable, countable, readable list that can also be checked.
 *
 * @since [*next-version*]
 */
class CountableMap extends AbstractBaseCountableMap
{
    /* Basic string i18n.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /* Factory of data store.
     *
     * @since [*next-version*]
     */
    use CreateDataStoreCapableTrait;

    /**
     * @since [*next-version*]
     *
     * @param ArrayObject|array|stdClass $elements The elements of the map.
     */
    public function __construct($elements)
    {
        if (is_array($elements) || ($elements instanceof stdClass)) {
            /* Normalizing to something that is both a writable container,
             * and iterable. This will avoid having to create a new iterator
             * object every time iteration is started, while still avoiding
             * having to copy the elements: `ArrayObject` will work with `stdClass`
             * references as is, and `arrays` enjoy the benefits of copy-on-write.
             */
            $elements = $this->_createDataStore($elements);
        }

        $this->_setDataStore($elements);

        $this->_construct();
    }
}
