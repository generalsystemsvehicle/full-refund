<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Api\Products;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ProductsTest extends TestCase
{
    /** @test */
    function it_returns_a_paginated_index()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__DIR__.'/../Fixtures/Products/index.json')),
        ]);

        $products = new Products(['mock' => $mock]);

        $response = $products->index();

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('num_items', $response));
        $this->assertTrue(array_key_exists('items', $response));
    }

    /** @test */
    function it_gets_a_single_record()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__DIR__.'/../Fixtures/Products/get.json')),
        ]);

        $products = new Products(['mock' => $mock]);

        $response = $products->get('L-999-70031');

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('vbid', $response));
    }

    /** @test */
    function it_gets_a_toc()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__DIR__.'/../Fixtures/Products/toc.json')),
        ]);

        $products = new Products(['mock' => $mock]);

        $response = $products->getTableOfContents('L-999-70031');

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('table_of_contents', $response));
    }
}
