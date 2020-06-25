<?php declare(strict_types=1);

namespace Dhii\Wp\Containers\Options;

use Dhii\Data\Container\WritableContainerInterface;
use Dhii\Wp\Containers\Exception\ContainerException;
use Dhii\Wp\Containers\Exception\NotFoundException;
use Dhii\Wp\Containers\Util\StringTranslatingTrait;
use Exception;
use RuntimeException;
use Throwable;
use UnexpectedValueException;

/**
 * Metadata for a particular site.
 *
 * @package Dhii\Wp\Containers\
 */
class SiteMeta implements WritableContainerInterface
{
    use StringTranslatingTrait;

    /**
     * @var int
     */
    protected $siteId;
    protected $default;

    /**
     * @param int $siteId ID of the site.
     * @param mixed $default The value that, if returned by WP, will indicate that the key is not found.
     */
    public function __construct(int $siteId, $default)
    {
        $this->siteId = $siteId;
        $this->default = $default;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        try {
            return $this->_getMeta($id);
        } catch (UnexpectedValueException $e) {
            throw new NotFoundException(
                $this->__('Meta key "%1$s" not found', [$id]),
                0,
                $e,
                $this,
                $id
            );
        } catch (Exception $e) {
            throw new ContainerException(
                $this->__('Could not get value for meta key "%1$s', [$id]),
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
            $this->_getMeta($id);

            return true;
        } catch (UnexpectedValueException $e) {
            return false;
        } catch (Exception $e) {
            throw new ContainerException(
                $this->__('Could not check for meta key "%1$s"', [$id]),
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
            $this->_setMeta($key, $value);
        } catch (Exception $e) {
            throw new ContainerException(
                $this->__('Could not set value for meta key "%1$s"', [$key]),
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
        $siteId = $this->siteId;
        $result = delete_network_option($siteId, $key);

        if ($result === false) {
            throw new ContainerException(
                $this->__('Could not delete meta key "%1$s"', [$key]),
                0,
                null,
                $this
            );
        }
    }

    /**
     * Retrieves a meta value.
     *
     * @param string $name The name of the meta key to retrieve.
     *
     * @return mixed The meta value.
     *
     * @throws UnexpectedValueException If the meta value matches the configured default.
     * @throws RuntimeException If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getMeta(string $name)
    {
        $siteId = $this->siteId;
        $default = $this->default;
        $value = get_network_option($siteId, $name, $default);

        if ($value === $default) {
            throw new UnexpectedValueException(
                $this->__(
                    'Meta key "%1$s" for blog #%2$d does not exist',
                    [$name, $siteId]
                )
            );
        }

        return $value;
    }

    /**
     * Assigns a value to a meta key.
     *
     * @param string $name The name of the meta key to set the value for.
     * @param mixed $value The value to set.
     *
     * @throws UnexpectedValueException If new meta value does not match what was being set.
     * @throws RuntimeException If problem setting.
     * @throws Throwable If problem running.
     */
    protected function _setMeta(string $name, $value)
    {
        $siteId = $this->siteId;

        $isSuccessful = update_network_option($siteId, $name, $value);
        if (!$isSuccessful) {
            $newValue = $this->_getMeta($name);
            $isSuccessful = $value === $newValue;
        }

        if (!$isSuccessful) {
            throw new UnexpectedValueException(
                $this->__(
                    'New meta value did not match the intended value: "%1$s" VS "%2$s"',
                    [
                        print_r($value, true),
                        print_r($newValue, true),
                    ]
                )
            );
        }
    }
}