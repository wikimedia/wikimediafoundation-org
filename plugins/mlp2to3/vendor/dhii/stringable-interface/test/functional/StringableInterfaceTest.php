<?php

namespace Dhii\Util\String\FuncTest;

use Xpmock\TestCase;

/**
 * Tests {@see \Dhii\Util\String\StringableInterface}.
 *
 * @since 0.1
 */
class StringableInterfaceTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since 0.1
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Util\\String\\StringableInterface';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @return ContextInterface
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->__toString()
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

        $this->assertInstanceOf(static::TEST_SUBJECT_CLASSNAME, $subject);
    }
}
