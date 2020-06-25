<?php

namespace Dhii\Di\UnitTest;

use Dhii\Di\ResolveDefinitionCapableTrait as TestSubject;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class ResolveDefinitionCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Di\ResolveDefinitionCapableTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock.
     *
     * @return MockObject The new instance.
     */
    public function createInstance($methods = [])
    {
        is_array($methods) && $methods = $this->mergeValues($methods, [
            '__',
        ]);

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
            ->setMethods($methods)
            ->getMockForTrait();

        $mock->method('__')
                ->will($this->returnArgument(0));

        return $mock;
    }

    /**
     * Merges the values of two arrays.
     *
     * The resulting product will be a numeric array where the values of both inputs are present, without duplicates.
     *
     * @since [*next-version*]
     *
     * @param array $destination The base array.
     * @param array $source      The array with more keys.
     *
     * @return array The array which contains unique values
     */
    public function mergeValues($destination, $source)
    {
        return array_keys(array_merge(array_flip($destination), array_flip($source)));
    }

    /**
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string   $className      Name of the class for the mock to extend.
     * @param string[] $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return MockBuilder The builder for a mock of an object that extends and implements
     *                     the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Creates a mock that uses traits.
     *
     * This is particularly useful for testing integration between multiple traits.
     *
     * @since [*next-version*]
     *
     * @param string[] $traitNames Names of the traits for the mock to use.
     *
     * @return MockBuilder The builder for a mock of an object that uses the traits.
     */
    public function mockTraits($traitNames = [])
    {
        $paddingClassName = uniqid('Traits');
        $definition = vsprintf('abstract class %1$s {%2$s}', [
            $paddingClassName,
            implode(
                ' ',
                array_map(
                    function ($v) {
                        return vsprintf('use %1$s;', [$v]);
                    },
                    $traitNames)),
        ]);
        var_dump($definition);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Creates a new exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return RootException|MockObject The new exception.
     */
    public function createException($message = '')
    {
        $mock = $this->getMockBuilder('Exception')
            ->setConstructorArgs([$message])
            ->getMock();

        return $mock;
    }

    /**
     * Creates a new Runtime exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return RootException|MockObject The new Runtime exception.
     */
    public function createRuntimeException($message = '')
    {
        $mock = $this->getMockBuilder('RuntimeException')
            ->setConstructorArgs([$message])
            ->getMock();

        return $mock;
    }

    /**
     * Creates a new invocable object.
     *
     * @since [*next-version*]
     *
     * @return MockObject An object that has an `__invoke()` method.
     */
    public function createCallable()
    {
        $mock = $this->getMockBuilder('MyCallable')
            ->setMethods(['__invoke'])
            ->getMock();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInternalType(
            'object',
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests that `_resolveDefinition()` works as expected when the definition is callable.
     *
     * @since [*next-version*]
     */
    public function testResolveDefinitionCallable()
    {
        $val = uniqid('val');
        $args = [uniqid('arg0'), uniqid('arg1')];
        $definition = $this->createCallable();
        $subject = $this->createInstance(['_getArgsForDefinition', '_normalizeArray', '_invokeCallable']);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_getArgsForDefinition')
            ->with($definition)
            ->will($this->returnValue($args));
        $subject->expects($this->exactly(1))
            ->method('_normalizeArray')
            ->with($args)
            ->will($this->returnArgument(0));
        $subject->expects($this->exactly(1))
            ->method('_invokeCallable')
            ->with($definition, $args)
            ->will($this->returnValue($val));

        $result = $_subject->_resolveDefinition($definition);
        $this->assertEquals($val, $result, 'Wrong definition resolution result');
    }

    /**
     * Tests that `_resolveDefinition()` works as expected when the definition is not callable.
     *
     * @since [*next-version*]
     */
    public function testResolveDefinitionNotCallable()
    {
        $definition = uniqid('definition');
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_resolveDefinition($definition);
        $this->assertEquals($definition, $result, 'Wrong definition resolution result');
    }

    /**
     * Tests that `_resolveDefinition()` fails as expected when problem resolving a callable definition.
     *
     * @since [*next-version*]
     */
    public function testResolveDefinitionFailure()
    {
        $definition = $this->createCallable();
        $exception = $this->createException('Could not get args');
        $runtimeException = $this->createRuntimeException('Could not resolve definition');
        $subject = $this->createInstance(['_getArgsForDefinition', '_createRuntimeException']);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_getArgsForDefinition')
            ->with($definition)
            ->will($this->throwException($exception));
        $subject->expects($this->exactly(1))
            ->method('_createRuntimeException')
            ->with(
                $this->isType('string'),
                null,
                $exception
            )
            ->will($this->returnValue($runtimeException));

        $this->setExpectedException('RuntimeException');
        $_subject->_resolveDefinition($definition);
    }
}
