<?php

namespace GeneralSystemsVehicle\VitalSource\Tests\Unit;

use GeneralSystemsVehicle\VitalSource\Api\Users;
use GeneralSystemsVehicle\VitalSource\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class UsersTest extends TestCase
{
    /** @test */
    function it_creates_a_reference_user()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Users/create.xml')),
        ]);

        $users = new Users(['mock' => $mock]);

        $response = $users->create([]);

        $this->assertTrue(is_null($response));

        $response = $users->create([
            'reference' => 'yourReferenceID',
        ]);

        $this->assertTrue(is_null($response));

        $response = $users->create([
            'reference' => 'yourReferenceID',
            'first-name' => 'Jose',
        ]);

        $this->assertTrue(is_null($response));

        $response = $users->create([
            'reference' => 'yourReferenceID',
            'first-name' => 'Jose',
            'last-name' => 'Tester',
        ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('email', $response));
        $this->assertTrue(array_key_exists('first-name', $response));
        $this->assertTrue(array_key_exists('last-name', $response));
    }

    /** @test */
    function it_gets_a_single_record()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Users/get.xml')),
        ]);

        $users = new Users(['mock' => $mock]);

        $response = $users->get('yourReferenceID');

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('email', $response));
        $this->assertTrue(array_key_exists('first-name', $response));
        $this->assertTrue(array_key_exists('last-name', $response));
    }

    /** @test */
    function it_verifies_a_user_exists()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Users/get.xml')),
        ]);

        $users = new Users(['mock' => $mock]);

        $response = $users->verify('yourReferenceID');

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('email', $response));
        $this->assertTrue(array_key_exists('first-name', $response));
        $this->assertTrue(array_key_exists('last-name', $response));
    }

    /** @test */
    function it_updates_a_record()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/xml'], file_get_contents(__DIR__.'/../Fixtures/Users/update.xml')),
        ]);

        $users = new Users(['mock' => $mock]);

        $response = $users->update('yourReferenceID', []);

        $this->assertTrue(is_null($response));

        $response = $users->update('yourReferenceID', [
                'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
            ]);

        $this->assertTrue(is_null($response));

        $response = $users->update('yourReferenceID', [
                'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
                'reference' => 'new-uuid',
            ]);

        $this->assertTrue(is_null($response));

        $response = $users->update('yourReferenceID', [
                'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
                'reference' => 'new-uuid',
                'first-name' => 'Jose',
            ]);

        $this->assertTrue(is_null($response));

        $response = $users->update('yourReferenceID', [
                'X-VitalSource-Access-Token' => 'lowercaseandnumbers',
                'reference' => 'new-uuid',
                'first-name' => 'Jose',
                'last-name' => 'Tester',
            ]);

        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('email', $response));
        $this->assertTrue(array_key_exists('first-name', $response));
        $this->assertTrue(array_key_exists('last-name', $response));
        $this->assertTrue(array_key_exists('access-token', $response));
    }
}
