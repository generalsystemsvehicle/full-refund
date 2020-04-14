<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Api\Redirects;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class RedirectsTest extends TestCase
{
    /** @test */
    function it_generates_an_sso_link()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Redirects/sso.xml')),
        ]);

        $redirects = new Redirects(['mock' => $mock]);

        $response = $redirects->sso([]);

        $this->assertTrue(is_null($response));

        $response = $redirects->sso([
            'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
        ]);

        $this->assertTrue(is_null($response));

        $response = $redirects->sso([
            'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
            'destination' => 'https://bookshelf.vitalsource.com/books/L-999-70031',
            'brand' => 'bookshelf.vitalsource.com',
        ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('@attributes', $response));

    }
}
