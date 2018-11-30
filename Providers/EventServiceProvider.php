<?php

namespace Modules\Imonitor\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Imonitor\Events\Handlers\SaveUserProducts;
use Modules\Imonitor\Events\ProductWasCreated;


class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProductWasCreated::class => [
           SaveUserProducts::class,
        ]


    ];
}