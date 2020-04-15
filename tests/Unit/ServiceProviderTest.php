<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Tests\Stubs\StubbedContract;
use GeneralSystemsVehicle\VitalSource\Tests\Stubs\StubbedEvent;
use GeneralSystemsVehicle\VitalSource\Tests\Stubs\StubbedImplementation;
use GeneralSystemsVehicle\VitalSource\Tests\Stubs\StubbedListener;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GeneralSystemsVehicle\VitalSource\VitalSourceServiceProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ServiceProviderTest extends TestCase
{
    /** @test */
    function it_sets_up_a_listener()
    {
        $dispatcher = $this->app->make(Dispatcher::class);

        $dispatcher->forget(StubbedEvent::class);

        $this->assertFalse($dispatcher->hasListeners(StubbedEvent::class));

        $provider = new VitalSourceServiceProvider($this->app);

        $this->setProperty($provider, 'events', [
            StubbedEvent::class => [
                StubbedListener::class,
            ],
        ]);

        $this->invokeMethod($provider, 'bootEvents', [ ]);

        $this->assertTrue($dispatcher->hasListeners(StubbedEvent::class));
    }

    /** @test */
    function it_cannot_bind_a_contract_without_an_implementation()
    {
        $this->expectException(BindingResolutionException::class);

        $this->app->make(StubbedContract::class);
    }

    /** @test */
    function it_binds_a_contract_to_an_implementation()
    {
        $provider = new VitalSourceServiceProvider($this->app);

        $this->setProperty($provider, 'serviceBindings', [
            StubbedContract::class => StubbedImplementation::class,
        ]);

        $this->invokeMethod($provider, 'registerServices');

        $contract = $this->app->make(StubbedContract::class);

        $this->assertTrue($contract instanceof StubbedImplementation);
    }
}
