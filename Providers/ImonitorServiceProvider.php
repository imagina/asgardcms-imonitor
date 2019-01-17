<?php

namespace Modules\Imonitor\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Imonitor\Events\Handlers\RegisterImonitorSidebar;

class ImonitorServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterImonitorSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('products', array_dot(trans('imonitor::products')));
            $event->load('variables', array_dot(trans('imonitor::variables')));
            $event->load('records', array_dot(trans('imonitor::records')));
            $event->load('alerts', array_dot(trans('imonitor::alerts')));
            // append translations




        });
    }

    public function boot()
    {
        $this->publishConfig('imonitor', 'config');
        $this->publishConfig('imonitor', 'settings');
        $this->publishConfig('imonitor', 'permissions');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Imonitor\Repositories\ProductRepository',
            function () {
                $repository = new \Modules\Imonitor\Repositories\Eloquent\EloquentProductRepository(new \Modules\Imonitor\Entities\Product());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Imonitor\Repositories\Cache\CacheProductDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Imonitor\Repositories\VariableRepository',
            function () {
                $repository = new \Modules\Imonitor\Repositories\Eloquent\EloquentVariableRepository(new \Modules\Imonitor\Entities\Variable());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Imonitor\Repositories\Cache\CacheVariableDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Imonitor\Repositories\RecordRepository',
            function () {
                $repository = new \Modules\Imonitor\Repositories\Eloquent\EloquentRecordRepository(new \Modules\Imonitor\Entities\Record());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Imonitor\Repositories\Cache\CacheRecordDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Imonitor\Repositories\AlertRepository',
            function () {
                $repository = new \Modules\Imonitor\Repositories\Eloquent\EloquentAlertRepository(new \Modules\Imonitor\Entities\Alert());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Imonitor\Repositories\Cache\CacheAlertDecorator($repository);
            }
        );
// add bindings




    }
}
