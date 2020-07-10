<?php # -*- coding: utf-8 -*-

/*
 * This file is part of the Product License package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\ProductPagesLicensing;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class RequestHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    /**
     * RequestHandler constructor.
     * @param ClientInterface $client
     * @param RequestFactoryInterface $requestFactory
     * @param UriFactoryInterface $uriFactory
     */
    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
    }

    /**
     * @param string $httpMethod
     * @param string $url
     * @return string
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function doRequest(string $httpMethod, string $url): string
    {
        $request = $this->requestFactory->createRequest(
            $httpMethod,
            $this->uriFactory->createUri($url)
        );

        $response = $this->client->sendRequest($request);
        $body = $response->getBody();

        return $body->getContents();
    }
}
