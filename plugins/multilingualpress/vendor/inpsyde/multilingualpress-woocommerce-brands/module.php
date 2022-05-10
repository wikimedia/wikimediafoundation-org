<?php

use Inpsyde\MultilingualPress\Framework\Service\ServiceProvider as ServiceProviderInterface;
use Inpsyde\MultilingualPress\WooCommerce\Brands\ServiceProvider;

return function (): ServiceProviderInterface
{
    return new ServiceProvider();
};
