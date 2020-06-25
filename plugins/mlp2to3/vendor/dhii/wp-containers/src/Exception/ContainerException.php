<?php declare(strict_types = 1);

namespace Dhii\Wp\Containers\Exception;

use Dhii\Data\Container\Exception\ContainerExceptionInterface;
use Exception;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * Basic implementation of container exception.
 *
 * @package Dhii\Wp\Containers
 */
class ContainerException extends Exception implements ContainerExceptionInterface
{

    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param Throwable|null $previous The inner exception, if any,
     * @param ContainerInterface|null $container The container that caused the exception, if any,
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null,
        ContainerInterface $container = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getContainer()
    {
        return $this->container;
    }

}
