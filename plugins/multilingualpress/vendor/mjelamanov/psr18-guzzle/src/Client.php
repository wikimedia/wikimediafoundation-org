<?php

namespace Mjelamanov\GuzzlePsr18;

use Exception;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Mjelamanov\GuzzlePsr18\Exception\GuzzleException;
use Mjelamanov\GuzzlePsr18\Exception\NetworkException;
use Mjelamanov\GuzzlePsr18\Exception\RequestException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client.
 */
class Client implements ClientInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $guzzle;

    /**
     * Client constructor.
     *
     * @param \GuzzleHttp\ClientInterface $guzzle
     */
    public function __construct(GuzzleClientInterface $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->guzzle->send($request);
        } catch (ConnectException $e) {
            throw new NetworkException($e->getRequest(), 'Could not connect to ' . $request->getUri(), $e);
        } catch (GuzzleRequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse();
            }

            throw new RequestException($e->getRequest(), 'No response returned for ' . $request->getUri(), $e);
        } catch (Exception $e) {
            throw new GuzzleException(
                'Something went wrong while send request to ' . $request->getUri(),
                0,
                $e
            );
        }
    }
}
