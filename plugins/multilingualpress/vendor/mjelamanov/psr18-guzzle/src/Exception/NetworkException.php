<?php

namespace Mjelamanov\GuzzlePsr18\Exception;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

/**
 * Class NetworkException.
 */
class NetworkException extends GuzzleException implements NetworkExceptionInterface
{
    use WithRequest;

    /**
     * NetworkException constructor.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(RequestInterface $request, string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->request = $request;
    }
}
