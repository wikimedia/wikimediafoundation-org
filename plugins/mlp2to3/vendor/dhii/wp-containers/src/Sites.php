<?php declare(strict_types = 1);

namespace Dhii\Wp\Containers;

use Dhii\Data\Container\ContainerInterface;
use Dhii\Wp\Containers\Exception\ContainerException;
use Dhii\Wp\Containers\Exception\NotFoundException;
use Dhii\Wp\Containers\Util\StringTranslatingTrait;
use Psr\Container\NotFoundExceptionInterface;
use WP_Site;

/**
 * Allows retrieval of WP site objects by ID.
 *
 * @package Dhii\Wp\Containers
 */
class Sites implements ContainerInterface
{

    use StringTranslatingTrait;

    /**
     * {@inheritDoc}
     *
     * @return WP_Site The site for the specified ID.
     */
    public function get($id)
    {
        $site = get_site($id);

        if (!$site) {
            throw new NotFoundException(
                $this->__('No site found for ID "%1$d"', [$id]),
                0,
                null,
                $this,
                (string) $id
            );
        }

        if (!($site instanceof WP_Site)) {
            throw new ContainerException(
                $this->__('Site #%1$d is invalid', [$id]),
                0,
                null,
                $this
            );
        }

        return $site;
    }

    /**
     * {@inheritDoc}
     */
    public function has($id)
    {
        try {
            $site = $this->get($id);
        } catch (NotFoundExceptionInterface $e) {
            return false;
        }

        return true;
    }

}