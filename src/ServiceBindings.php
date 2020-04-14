<?php

namespace GeneralSystemsVehicle\VitalSource;

trait ServiceBindings
{
    /**
     * All of the service bindings for package.
     *
     * @var array
     */
    protected $serviceBindings = [
        Api\Catalog::class,
        Api\Codes::class,
        Api\Credentials::class,
        Api\Licenses::class,
        Api\Products::class,
        Api\Redemptions::class,
        Api\Redirects::class,
        Api\Users::class,
    ];
}
