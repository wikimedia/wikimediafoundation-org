<?php

namespace Mjelamanov\GuzzlePsr18\Exception;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

/**
 * Class RequestException.
 */
class RequestException extends GuzzleException implements RequestExceptionInterface
{
    use WithRequest;

    /**
     * RequestException constructor.
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
