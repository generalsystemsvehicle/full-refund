<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Api\Credentials;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class CredentialsTest extends TestCase
{
    /** @test */
    function it_resets_credentials()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml']),
        ]);

        $credentials = new Credentials(['mock' => $mock]);

        $response = $credentials->reset([]);

        $this->assertTrue(is_null($response));

        $response = $credentials->reset([
            'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
        ]);

        $this->assertTrue(is_null($response));
    }

    /** @test */
    function it_creates_credentials_from_reference()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Credentials/create.xml')),
        ]);

        $credentials = new Credentials(['mock' => $mock]);

        $response = $credentials->create([]);

        $this->assertTrue(is_null($response));

        $response = $credentials->create([
            'reference' => 'yourReferenceID',
        ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('credential', $response));
        $this->assertTrue(array_key_exists('@attributes', $response['credential']));
        $this->assertTrue(array_key_exists('email', $response['credential']['@attributes']));
        $this->assertTrue(array_key_exists('access-token', $response['credential']['@attributes']));
        $this->assertTrue(array_key_exists('guid', $response['credential']['@attributes']));
        $this->assertTrue(array_key_exists('reference', $response['credential']['@attributes']));
    }

    /** @test */
    function it_gets_credentials_from_reference()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Credentials/create.xml')),
        ]);

        $credentials = new Credentials(['mock' => $mock]);

        $response = $credentials->get([]);

        $this->assertTrue(is_null($response));

        $response = $credentials->get([
            'reference' => 'yourReferenceID',
        ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('credential', $response));
        $this->assertTrue(array_key_exists('@attributes', $response['credential']));
        $this->assertTrue(array_key_exists('email', $response['credential']['@attributes']));
        $this->assertTrue(array_key_exists('access-token', $response['credential']['@attributes']));
        $this->assertTrue(array_key_exists('guid', $response['credential']['@attributes']));
        $this->assertTrue(array_key_exists('reference', $response['credential']['@attributes']));
    }

    /** @test */
    function it_verifies_credentials_from_reference()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Credentials/create.xml')),
        ]);

        $credentials = new Credentials(['mock' => $mock]);

        $response = $credentials->verify([]);

        $this->assertTrue(is_null($response));

        $response = $credentials->verify([
            'reference' => 'yourReferenceID',
        ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('credential', $response));
        $this->assertTrue(array_key_exists('@attributes', $response['credential']));
        $this->assertTrue(array_key_exists('email', $response['credential']['@attributes']));
        $this->assertTrue(array_key_exists('access-token', $response['credential']['@attributes']));
        $this->assertTrue(array_key_exists('guid', $response['credential']['@attributes']));
        $this->assertTrue(array_key_exists('reference', $response['credential']['@attributes']));
    }
}
