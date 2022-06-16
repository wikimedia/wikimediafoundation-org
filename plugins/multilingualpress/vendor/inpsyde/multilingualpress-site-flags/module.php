<?php

use Inpsyde\MultilingualPress\Framework\Service\ServiceProvider as ServiceProviderInterface;
use Inpsyde\MultilingualPress\SiteFlags\ServiceProvider;

return function (): ServiceProviderInterface
{
    return new ServiceProvider();
};
