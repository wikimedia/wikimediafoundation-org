<?php

namespace Dhii\Data\FuncTest;

/**
 * Tests {@see \Dhii\Data\KeyValueAwareInterface
 *
 * @since 0.1
 */
class KeyValueAwareInterfaceTest extends \Xpmock\TestCase
{
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Data\\ValueAwareInterface';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @return \Dhii\Data\KeyValueAwareInterface A new instance of the test subject.
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->getKey()
            ->getValue()
            ->new();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since 0.1
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf(static::TEST_SUBJECT_CLASSNAME, $subject, 'A valid instance of the test subject could not be created');
    }
}
