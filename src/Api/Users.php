<?php

namespace GeneralSystemsVehicle\VitalSource\Api;

use Illuminate\Support\Arr;
use GeneralSystemsVehicle\VitalSource\Guzzle\Api;

class Users extends Api
{
    /**
     * Create a reference user.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function create($payload = [])
    {
        if (! Arr::has($payload, 'reference')) {
            return null;
        }

        if (! Arr::has($payload, 'first-name')) {
            return null;
        }

        if (! Arr::has($payload, 'last-name')) {
            return null;
        }

        return $this->try(function() use ($payload)
        {
            $body = '<?xml version="1.0" encoding="UTF-8"?>
                <user>
                    <reference>' . Arr::get($payload, 'reference') . '</reference>
                    <first-name>' . Arr::get($payload, 'first-name') . '</first-name>
                    <last-name>' . Arr::get($payload, 'last-name') . '</last-name>
                </user>';

            return $this->client->post('v3/users', [
                'body' => $body,
            ]);
        });
    }

    /**
     * Get / verify a user.
     *
     * @param  string $id
     * @return array|null
     */
    public function verify($id)
    {
        return $this->get($id);
    }

    /**
     * Get / verify a user.
     *
     * @param  string $id
     * @return array|null
     */
    public function get($id)
    {
        return $this->try(function() use ($id)
        {
            return $this->client->get('v3/users/' . $id, [
                'query' => [
                    'full' => 'true',
                ],
            ]);
        });
    }

    /**
     * Update a user.
     *
     * @param  string $id
     * @param  array  $payload
     * @return array|null
     */
    public function update($id, $payload = [])
    {
        if (! Arr::has($payload, 'X-VitalSource-Access-Token')) {
            return null;
        }

        if (! Arr::has($payload, 'reference')) {
            return null;
        }

        if (! Arr::has($payload, 'first-name')) {
            return null;
        }

        if (! Arr::has($payload, 'last-name')) {
            return null;
        }

        return $this->try(function() use ($id, $payload)
        {
            $body = '<?xml version="1.0" encoding="UTF-8"?>
                <user>
                    <reference>' . Arr::get($payload, 'reference') . '</reference>
                    <first-name>' . Arr::get($payload, 'first-name') . '</first-name>
                    <last-name>' . Arr::get($payload, 'last-name') . '</last-name>
                </user>';

            return $this->client->put('v3/users/' . $id, [
                'body' => $body,
                'headers' => [
                    'X-VitalSource-Access-Token' => Arr::get($payload, 'X-VitalSource-Access-Token'),
                ],
            ]);
        });
    }
}
