<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Api\Codes;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class CodesTest extends TestCase
{
    /** @test */
    function it_creates_a_code()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Codes/create.xml')),
        ]);

        $codes = new Codes(['mock' => $mock]);

        $response = $codes->create([]);

        $this->assertTrue(is_null($response));

        $response = $codes->create([
            'sku' => 'L-999-70031',
        ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('code', $response));
    }

    /** @test */
    function it_gets_a_single_record()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Codes/get.xml')),
        ]);

        $codes = new Codes(['mock' => $mock]);

        $response = $codes->get('THECODEYOUSENTWILLBEHERE');

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('id', $response));
        $this->assertTrue(array_key_exists('product-name', $response));
        $this->assertTrue(array_key_exists('product-sku', $response));
    }

    /** @test */
    function it_deletes_a_single_record()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml']),
        ]);

        $codes = new Codes(['mock' => $mock]);

        $response = $codes->delete('THECODEYOUSENTWILLBEHERE');

        $this->assertTrue(is_null($response));
    }

    /** @test */
    function it_refunds_a_single_record()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml']),
        ]);

        $codes = new Codes(['mock' => $mock]);

        $response = $codes->refund('THECODEYOUSENTWILLBEHERE');

        $this->assertTrue(is_null($response));
    }

    /** @test */
    function it_cancels_a_single_record()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml']),
        ]);

        $codes = new Codes(['mock' => $mock]);

        $response = $codes->cancel('THECODEYOUSENTWILLBEHERE');

        $this->assertTrue(is_null($response));
    }
}
