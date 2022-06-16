<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Framework\Service\Exception;

/**
 * Exception base class for all exceptions thrown by the container.
 *
 * This is necessary to be able to catch all exceptions thrown in the module in one go.
 * Moreover, compliance with PSR-11 would be easier, with pretty much no code necessary.
 */
class ContainerException extends \Exception
{

}
