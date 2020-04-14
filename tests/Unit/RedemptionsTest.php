<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Api\Redemptions;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class RedemptionsTest extends TestCase
{
    /** @test */
    function it_creates_a_code_redemption()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Redemptions/create.xml')),
        ]);

        $redemptions = new Redemptions(['mock' => $mock]);

        $response = $redemptions->create([]);

        $this->assertTrue(is_null($response));

        $response = $redemptions->create([
            'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
            'code' => 'THECODEYOUSENTWILLBEHERE',
        ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('item', $response));
        $this->assertTrue(array_key_exists('@attributes', $response['item']));
    }

    /** @test */
    function it_redeems_a_code()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Redemptions/create.xml')),
        ]);

        $redemptions = new Redemptions(['mock' => $mock]);

        $response = $redemptions->redeem([]);

        $this->assertTrue(is_null($response));

        $response = $redemptions->redeem([
            'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
        ]);

        $this->assertTrue(is_null($response));

        $response = $redemptions->redeem([
            'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
            'code' => 'THECODEYOUSENTWILLBEHERE',
        ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('item', $response));
        $this->assertTrue(array_key_exists('@attributes', $response['item']));
    }
}
