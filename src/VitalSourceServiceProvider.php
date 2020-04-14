<?php

namespace GeneralSystemsVehicle\VitalSource;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class VitalSourceServiceProvider extends ServiceProvider
{
    use EventMap;
    use ServiceBindings;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootEvents();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerServices();
    }

    /**
     * Register the package events.
     *
     * @return void
     */
    protected function bootEvents()
    {
        $dispatcher = $this->app->make(Dispatcher::class);

        foreach ($this->events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }

    /**
     * Setup the configuration for the package.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'vitalsource'
        );

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('vitalsource.php'),
        ], 'vitalsource');
    }

    /**
     * Register package services in the container.
     *
     * @return void
     */
    protected function registerServices()
    {
        if (! property_exists($this, 'serviceBindings')) {
            return;
        }

        foreach ($this->serviceBindings as $key => $value) {
            is_numeric($key)
                    ? $this->app->singleton($value)
                    : $this->app->singleton($key, $value);
        }
    }
}
