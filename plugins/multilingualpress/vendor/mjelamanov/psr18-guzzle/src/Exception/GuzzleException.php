<?php

namespace Mjelamanov\GuzzlePsr18\Exception;

use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

/**
 * Class GuzzleException.
 */
class GuzzleException extends RuntimeException implements ClientExceptionInterface
{
}
