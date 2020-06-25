<?php

namespace Dhii\Di\UnitTest;

use Dhii\Cache\ContainerInterface;
use Dhii\Di\GetServiceCapableCachingTrait as TestSubject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class GetServiceCapableCachingTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Di\GetServiceCapableCachingTrait';

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
     * Creates a new Container exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return MockObject|RootException|ContainerExceptionInterface The new exception.
     */
    public function createContainerException($message = '')
    {
        $mock = $this->mockClassAndInterfaces('Exception', ['Psr\Container\ContainerExceptionInterface'])
            ->setConstructorArgs([$message])
            ->getMock();

        return $mock;
    }

    /**
     * Creates a new Not Found exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return MockObject|RootException|NotFoundExceptionInterface The new exception.
     */
    public function createNotFoundException($message = '')
    {
        $mock = $this->mockClassAndInterfaces('Exception', ['Psr\Container\NotFoundExceptionInterface'])
            ->setConstructorArgs([$message])
            ->getMock();

        return $mock;
    }

    /**
     * Creates a new cache container.
     *
     * @param array|null $methods The methods to mock, if any.
     *
     * @since [*next-version*]
     *
     * @return MockObject|ContainerInterface The new cache container.
     */
    public function createCacheContainer($methods = [])
    {
        is_array($methods) && $methods = $this->mergeValues($methods, [
            '__',
        ]);

        $mock = $this->getMockBuilder('Dhii\Cache\ContainerInterface')
            ->setMethods($methods)
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
     * Tests that `_getService()` works as expected.
     *
     * @since [*next-version*]
     */
    public function testGetService()
    {
        $key = uniqid('key');
        $definition = uniqid('definition');
        $service = uniqid('service');
        $cache = $this->createCacheContainer(['get', 'has']);
        $subject = $this->createInstance(['_getServiceCache', '_get', '_resolveDefinition']);
        $_subject = $this->reflect($subject);

        $cache->expects($this->exactly(1))
            ->method('get')
            ->with($key)
            ->will($this->returnCallback(function ($key, $generator) {
                return call_user_func_array($generator, [$key]);
            }));

        $subject->expects($this->exactly(1))
            ->method('_getServiceCache')
            ->will($this->returnValue($cache));
        $subject->expects($this->exactly(1))
            ->method('_get')
            ->with($key)
            ->will($this->returnValue($definition));
        $subject->expects($this->exactly(1))
            ->method('_resolveDefinition')
            ->with($definition)
            ->will($this->returnValue($service));

        $result = $_subject->_getService($key);
        $this->assertEquals($service, $result, 'Wrong service retrieved');
    }

    /**
     * Tests that `_getService()` fails as expected if cache retrieval results in an error.
     *
     * @since [*next-version*]
     */
    public function testGetServiceFailure()
    {
        $key = uniqid('key');
        $exception = $this->createException('Could not get value from cache');
        $containerException = $this->createContainerException('Could not get service');
        $cache = $this->createCacheContainer(['get', 'has']);
        $subject = $this->createInstance(['_getServiceCache', '_throwContainerException']);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_getServiceCache')
            ->will($this->returnValue($cache));
        $cache->expects($this->exactly(1))
            ->method('get')
            ->with($key)
            ->will($this->throwException($exception));
        $subject->expects($this->exactly(1))
            ->method('_throwContainerException')
            ->with(
                $this->isType('string'),
                null,
                $exception,
                true
            )
            ->will($this->throwException($containerException));

        $this->setExpectedException('Psr\Container\ContainerExceptionInterface');
        $_subject->_getService($key);
    }

    /**
     * Tests that `_getService()` fails as expected if cache retrieval results in a Not Found exception..
     *
     * @since [*next-version*]
     */
    public function testGetServiceFailureNotFound()
    {
        $key = uniqid('key');
        $exception = $this->createException('Could not get value from cache');
        $notFoundException = $this->createNotFoundException('Service not found');
        $cache = $this->createCacheContainer(['get', 'has']);
        $subject = $this->createInstance(['_getServiceCache', '_throwContainerException']);
        $_subject = $this->reflect($subject);

        $cache->expects($this->exactly(1))
            ->method('get')
            ->with($key)
            ->will($this->returnCallback(function ($key, $generator) {
                return call_user_func_array($generator, [$key]);
            }));

        $subject->expects($this->exactly(1))
            ->method('_getServiceCache')
            ->will($this->returnValue($cache));
        $subject->expects($this->exactly(1))
            ->method('_get')
            ->with($key)
            ->will($this->throwException($notFoundException));

        $this->setExpectedException('Psr\Container\NotFoundExceptionInterface');
        $_subject->_getService($key);
    }
}
