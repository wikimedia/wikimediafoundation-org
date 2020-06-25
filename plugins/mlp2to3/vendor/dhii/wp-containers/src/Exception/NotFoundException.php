<?php declare(strict_types = 1);

namespace Dhii\Wp\Containers\Exception;

use Dhii\Data\Container\Exception\NotFoundExceptionInterface;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * Basic implementation of container exception.
 *
 * @package Dhii\Wp\Containers
 */
class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{

    /**
     * @var ContainerInterface|null
     */
    protected $container;
    /**
     * @var string|null
     */
    protected $dataKey;

    /**
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param Throwable|null $previous The inner exception, if any,
     * @param ContainerInterface|null $container The container that caused the exception, if any,
     * @param string|null $dataKey The key that is not found.
     */
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null,
        ContainerInterface $container = null,
        string $dataKey = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->container = $container;
        $this->dataKey = $dataKey;
    }

    /**
     * {@inheritDoc}
     *
     * @return string The key.
     */
    public function getDataKey()
    {
        return $this->dataKey;
    }
}
