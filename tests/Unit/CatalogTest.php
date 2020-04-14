<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Api\Catalog;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class CatalogTest extends TestCase
{
    /** @test */
    function it_returns_a_paginated_index()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Catalog/index.xml')),
        ]);

        $catalog = new Catalog(['mock' => $mock]);

        $response = $catalog->index([
            'page' => 0,
            'per-page' => 1000,
        ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('@attributes', $response));
        $this->assertTrue(array_key_exists('item', $response));
    }
}
