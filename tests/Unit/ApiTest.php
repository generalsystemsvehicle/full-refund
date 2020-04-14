<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Guzzle\Api;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ApiTest extends TestCase
{
    /** @test */
    function it_handles_a_json_response()
    {
        $api = new Api();

        $response = new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__DIR__.'/../Fixtures/Products/get.json'));

        $response = $this->invokeMethod($api, 'handleReponse', [ $response ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('vbid', $response));
        $this->assertTrue(array_key_exists('title', $response));
    }

    /** @test */
    function it_catches_a_request_exception()
    {
        $api = new Api();

        $response = function () {
            throw new ClientException('+1 second delay', new Request('get', 'test'), new Response(404));
        };

        $response = $this->invokeMethod($api, 'try', [ $response ]);

        $this->assertTrue(is_null($response));
    }

    /** @test */
    function it_handles_an_empty_json_response()
    {
        $api = new Api();

        $response = new Response(200, ['Content-Type' => 'application/json']);

        $response = $this->invokeMethod($api, 'handleReponse', [ $response ]);

        $this->assertTrue(is_null($response));
    }

    /** @test */
    function it_handles_an_xml_response()
    {
        $api = new Api();

        $response = new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Codes/get.xml'));

        $response = $this->invokeMethod($api, 'handleReponse', [ $response ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('id', $response));
        $this->assertTrue(array_key_exists('product-name', $response));
        $this->assertTrue(array_key_exists('product-sku', $response));
    }

    /** @test */
    function it_handles_an_empty_xml_response()
    {
        $api = new Api();

        $response = new Response(200, ['Content-Type' => 'application/xml']);

        $response = $this->invokeMethod($api, 'handleReponse', [ $response ]);

        $this->assertTrue(is_null($response));
    }

    /** @test */
    function it_handles_a_404_response()
    {
        $api = new Api();

        $exception = new RequestException(
            'Page not found',
            new Request('GET', 'test'),
            new Response(404, ['Content-Type' => 'application/json'])
        );

        $response = $this->invokeMethod($api, 'handleBadResponse', [ $exception ]);

        $this->assertTrue(is_null($response));
    }

    /** @test */
    function it_handles_a_422_response()
    {
        $api = new Api();

        $exception = new RequestException(
            'Unprocessable Entity',
            new Request('GET', 'test'),
            new Response(422, ['Content-Type' => 'application/json'])
        );

        $response = $this->invokeMethod($api, 'handleBadResponse', [ $exception ]);

        $this->assertTrue(is_null($response));
    }

    /** @test */
    function it_throws_an_exception_for_other_bad_responses()
    {
        $this->expectException(RequestException::class);

        $api = new Api();

        $exception = new RequestException(
            'Forbidden',
            new Request('GET', 'test'),
            new Response(403, ['Content-Type' => 'application/json'])
        );

        $response = $this->invokeMethod($api, 'handleBadResponse', [ $exception ]);

        $this->assertTrue(is_null($response));
    }
}
