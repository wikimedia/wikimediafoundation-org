<?php declare(strict_types = 1);

namespace Dhii\Wp\Containers\Options;

use Dhii\Data\Container\WritableContainerInterface;
use Dhii\Wp\Containers\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Dhii\Wp\Containers\Util\StringTranslatingTrait;
use Exception;
use Dhii\Data\Container\ContainerInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Throwable;
use WP_Site; // Counting on this being there when in WordPress

/**
 * Creates and returns option containers for sites.
 *
 * @package Dhii\Wp\Containers
 */
class BlogOptionsContainer implements ContainerInterface
{

    use StringTranslatingTrait;

    /**
     * @var callable
     */
    protected $optionsFactory;
    /**
     * @var BaseContainerInterface
     */
    protected $sitesContainer;

    /**
     * @param callable $optionsFactory A callable with the following signature:
     * `function (int $id): ContainerInterface`
     * Accepts a site ID, and returns a container with options for that site.
     * @param BaseContainerInterface $sitesContainer The container of WP Site instances.
     * Used for checking if a site exists.
     */
    public function __construct(
        callable $optionsFactory,
        BaseContainerInterface $sitesContainer
    ) {
        $this->optionsFactory = $optionsFactory;
        $this->sitesContainer = $sitesContainer;
    }

    /**
     * Retrieves options for a site with the specified ID.
     *
     * @param int The ID of the site to retrieve options for.
     *
     * @return WritableContainerInterface The options.
     */
    public function get($id)
    {
        $site = $this->_getSite($id);
        $id = (int) $site->blog_id;

        try {
            $options = $this->_createOptions($id);
        } catch (Exception $e) {
            throw new ContainerException(
                $this->__('Could not get options for site #%1$d', [$id]),
                0,
                $e,
                $this
            );
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        try {
            $this->_getSite($id);
        } catch (NotFoundExceptionInterface $e) {
            return false;
        } catch (Exception $e) {
            throw new ContainerException(
                $this->__('Could not check for option "%1$s"', [$id]),
                0,
                $e,
                $this
            );
        }

        return true;
    }

    /**
     * Retrieve a site instance for the specified ID.
     *
     * @param int|string $id The ID of the site to retrieve.
     * @return WP_Site The site instance.
     * @throws NotFoundExceptionInterface If site does not exist.
     * @throws Exception If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getSite($id): WP_Site
    {
        $site = $this->sitesContainer->get($id);

        return $site;
    }

    /**
     * Creates a container that represents options for a specific site.
     *
     * @param int $siteId The ID of the site to get the options for.
     * @return WritableContainerInterface The options.
     * @throws Exception If problem creating.
     */
    protected function _createOptions(int $siteId): WritableContainerInterface
    {
        $factory = $this->optionsFactory;

        if (!is_callable($factory)) {
            throw new Exception(
                $this->__('Could not invoke options factory'),
                null,
                null
            );
        }

        $options = $factory($siteId);

        return $options;
    }

}
