<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Api\Licenses;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class LicensesTest extends TestCase
{
    /** @test */
    function it_gets_a_list_of_licenses()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Licenses/get.xml')),
        ]);

        $licenses = new Licenses(['mock' => $mock]);

        $response = $licenses->get();

        $this->assertTrue(is_null($response));

        $response = $licenses->get([
            'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
        ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('license', $response));
        $this->assertTrue(array_key_exists('@attributes', $response['license'][0]));
    }
}
