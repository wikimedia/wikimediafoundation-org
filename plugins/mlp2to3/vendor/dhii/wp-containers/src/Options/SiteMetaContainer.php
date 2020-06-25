<?php


namespace Dhii\Wp\Containers\Options;

use Dhii\Data\Container\ContainerInterface;
use Dhii\Data\Container\WritableContainerInterface;
use Dhii\Wp\Containers\Exception\ContainerException;
use Dhii\Wp\Containers\Util\StringTranslatingTrait;
use Exception;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;
use WP_Site;

/**
 * Creates and returns metadata containers for sites.
 *
 * @package Dhii\Wp\Containers
 */
class SiteMetaContainer implements ContainerInterface
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
     * Accepts a site ID, and returns a container with meta for that site.
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
     * Retrieves metadata for a site with the specified ID.
     *
     * @param int The ID of the site to retrieve metadata for.
     *
     * @return WritableContainerInterface The metadata.
     */
    public function get($id)
    {
        $site = $this->_getSite($id);
        $id = (int) $site->blog_id;

        try {
            $options = $this->_createMeta($id);
        } catch (Exception $e) {
            throw new ContainerException(
                $this->__('Could not get meta for site #%1$d', [$id]),
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
                $this->__('Could not check for meta key "%1$s"', [$id]),
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
     * @throws NotFoundExceptionInterface If problem retrieving.
     * @throws Exception If problem retrieving.
     * @throws Throwable If problem running.
     */
    protected function _getSite($id): WP_Site
    {
        $site = $this->sitesContainer->get($id);

        return $site;
    }

    /**
     * Creates a container that represents metadata for a specific site.
     *
     * @param int $siteId The ID of the site to get the metadata for.
     *
     * @return WritableContainerInterface The metadata.
     *
     * @throws Exception If problem creating.
     */
    protected function _createMeta(int $siteId): WritableContainerInterface
    {
        $factory = $this->optionsFactory;

        if (!is_callable($factory)) {
            throw new Exception(
                $this->__('Could not invoke metadata factory'),
                null,
                null
            );
        }

        $options = $factory($siteId);

        return $options;
    }

}