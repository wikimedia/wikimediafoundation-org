<?php

namespace Mjelamanov\GuzzlePsr18\Exception;

use Psr\Http\Message\RequestInterface;

/**
 * Trait WithRequest.
 */
trait WithRequest
{
    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * {@inheritdoc}
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
