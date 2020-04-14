<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Guzzle\Backoff;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * @author Guillaume Cavana <guillaume.cavana@gmail.com>
 */
class BackoffTest extends TestCase
{
    /** @test */
    public function it_retries_after_a_connect_exception()
    {
        $mock = new MockHandler(
            [
                new ConnectException('Error 1', new Request('get', 'test')),
                new Response(200, ['X-Foo' => 'Bar']),
            ]
        );

        $handler = HandlerStack::create($mock);

        Backoff::$isDelayEnabled = false;

        $handler->push(Middleware::retry(Backoff::decider(), Backoff::delay()));

        $client = new Client(['handler' => $handler]);

        $this->assertEquals(200, $client->request('get', '/')->getStatusCode());
    }

    /** @test */
    public function it_fails_to_retry_after_limit()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Error 5');

        $mock = new MockHandler(
            [
                new ClientException('Error 0', new Request('get', 'test'), new Response(429)),
                new ClientException('Error 1', new Request('get', 'test'), new Response(429)),
                new ClientException('Error 2', new Request('get', 'test'), new Response(429)),
                new ClientException('Error 3', new Request('get', 'test'), new Response(429)),
                new ClientException('Error 4', new Request('get', 'test'), new Response(429)),
                new ClientException('Error 5', new Request('get', 'test'), new Response(429)),
            ]
        );

        $handler = HandlerStack::create($mock);

        Backoff::$isDelayEnabled = false;

        $handler->push(Middleware::retry(Backoff::decider(), Backoff::delay()));

        $client = new Client(['handler' => $handler]);

        $client->request('get', 'test')->getStatusCode();
    }

    /** @test */
    public function it_retries_after_delay()
    {
        $mock = new MockHandler(
            [
                new ConnectException('+1 second delay', new Request('get', 'test')),
                new Response(200),
            ]
        );

        $handler = HandlerStack::create($mock);

        $delayCalls = 0;
        $delayCallback = function ($retries, Response $response = null) use (&$delayCalls) {
            $delayCalls++;

            return Backoff::delay()($retries, $response);
        };

        $handler->push(Middleware::retry(Backoff::decider(), $delayCallback));

        $container = [];
        $handler->push(Middleware::history($container));

        $client = new Client(['handler' => $handler]);


        $this->assertEquals(200, $client->request('get', '/')->getStatusCode());
        $this->assertEquals(2, count($container));
        $this->assertEquals(1, $delayCalls);
    }

    /** @test */
    public function it_retries_429_errors()
    {
        $mock = new MockHandler(
            [
                new RequestException('Error 1', new Request('get', 'test'), new Response(429)),
                new Response(200),
            ]
        );

        $handler = HandlerStack::create($mock);

        Backoff::$isDelayEnabled = false;

        $handler->push(Middleware::retry(Backoff::decider(), Backoff::delay()));

        $container = [];
        $handler->push(Middleware::history($container));

        $client = new Client(['handler' => $handler]);

        $this->assertEquals(200, $client->request('get', '/')->getStatusCode());
        $this->assertEquals(2, count($container));
    }

    /** @test */
    public function it_retries_500_errors()
    {
        $mock = new MockHandler(
            [
                new ServerException('Error 1', new Request('get', 'test'), new Response(500)),
                new Response(200),
            ]
        );

        $handler = HandlerStack::create($mock);

        Backoff::$isDelayEnabled = false;

        $handler->push(Middleware::retry(Backoff::decider(), Backoff::delay()));

        $container = [];
        $handler->push(Middleware::history($container));

        $client = new Client(['handler' => $handler]);

        $this->assertEquals(200, $client->request('get', '/')->getStatusCode());
        $this->assertEquals(2, count($container));
    }

    /** @test */
    public function it_calculates_delay()
    {
        Backoff::$isDelayEnabled = true;

        $delay = Backoff::delay()(1, new Response(429));

        $this->assertGreaterThanOrEqual(1000, $delay);
    }
}
