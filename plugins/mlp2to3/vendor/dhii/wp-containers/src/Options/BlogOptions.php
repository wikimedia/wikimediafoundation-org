<?php declare(strict_types = 1);

namespace Dhii\Wp\Containers\Options;

use Dhii\Data\Container\WritableContainerInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Wp\Containers\Exception\ContainerException;
use Dhii\Wp\Containers\Exception\NotFoundException;
use Dhii\Wp\Containers\Util\StringTranslatingTrait;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use RuntimeException;
use Throwable;
use UnexpectedValueException;

/**
 * Allows access to options for a particular site.
 *
 * @package Dhii\Wp\Containers
 */
class BlogOptions implements WritableContainerInterface
{

    use StringTranslatingTrait;

    /**
     * @var int
     */
    protected $blogId;
    /** @var string */
    protected $default;

    /**
     * @param int $blogId The ID of the blog to represent the options for.
     * @param string $default The value to return if an option is not found.
     * This is necessary because WP will otherwise return `false`, which
     * is indistinguishable from a real option value.
     * Therefore, this should be set to something that is unlikely to be a
     * valid option value.
     */
    public function __construct(
        int $blogId,
        string $default
    ) {
        $this->blogId = $blogId;
        $this->default = $default;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        try {
            return $this->_getOption($id);
        } catch (UnexpectedValueException $e) {
            throw new NotFoundException(
                $this->__('Key "%1$s" not found', [$id]),
                0,
                $e,
                $this,
                $id
            );
        } catch (Exception $e) {
            throw new ContainerException(
                $this->__('Could not get value for key "%1$s', [$id]),
                0,
                $e,
                $this
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        try {
            $this->_getOption($id);

            return true;
        } catch (UnexpectedValueException $e) {
            return false;
        } catch (Exception $e) {
            throw new ContainerException(
                $this->__('Could not check for key "%1$s"', [$id]),
                0,
                $e,
                $this
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        try {
            $this->_setOption($key, $value);
        } catch (Exception $e) {
            throw new ContainerException(
                $this->__('Could not set value for key "%1$s"', [$key]),
                0,
                $e,
                $this
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete($key)
    {
        $blogId = $this->blogId;
        $result = delete_blog_option($blogId, $key);

        if ($result === false) {
            throw new ContainerException(
                $this->__('Could not delete option "%1$s"', [$key]),
                0,
                null,
                $this
            );
        }
    }

    /**
     * Retrieves an option value.
     *
     * @param string $name The name of the option to retrieve.
     *
     * @return mixed The option value.
     *
     * @throws UnexpectedValueException If the option value matches the configured default.
     * @throws RuntimeException If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getOption(string $name)
    {
        $blogId = $this->blogId;
        $default = $this->default;
        $value = get_blog_option($blogId, $name, $default);

        if ($value === $default) {
            throw new UnexpectedValueException(
                $this->__(
                    'Option "%1$s" for blog #%2$d does not exist',
                    [$name, $blogId]
                )
            );
        }

        return $value;
    }

    /**
     * Assigns a value to an option.
     *
     * @param string $name The name of the option to set the value for.
     * @param mixed $value The value to set.
     *
     * @throws UnexpectedValueException If new option value does not match what was being set.
     * @throws RuntimeException If problem setting.
     * @throws Throwable If problem running.
     */
    protected function _setOption(string $name, $value)
    {
        $blogId = $this->blogId;

        $isSuccessful = update_blog_option($blogId, $name, $value);
        if (!$isSuccessful) {
            $newValue = $this->_getOption($name);
            $isSuccessful = $value === $newValue;
        }

        if (!$isSuccessful) {
            throw new UnexpectedValueException(
                $this->__(
                    'New option value did not match the intended value: "%1$s" VS "%2$s"',
                    [
                        print_r($value, true),
                        print_r($newValue, true),
                    ]
                )
            );
        }
    }
}
