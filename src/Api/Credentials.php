<?php

namespace GeneralSystemsVehicle\VitalSource\Api;

use Illuminate\Support\Arr;
use GuzzleHttp\Exception\RequestException;
use GeneralSystemsVehicle\VitalSource\Guzzle\Api;

class Credentials extends Api
{
    /**
     * Reset credentials for a user.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function reset($payload = [])
    {
        if (! Arr::has($payload, 'X-VitalSource-Access-Token')) {
            return null;
        }

        return $this->try(function() use ($payload)
        {
            return $this->client->post('v3/users/reset_access', [
                'headers' => [
                    'X-VitalSource-Access-Token' => Arr::get($payload, 'X-VitalSource-Access-Token'),
                ],
            ]);
        });
    }

    /**
     * Get / verify / create credentials for a user.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function get($payload = [])
    {
        return $this->create($payload);
    }

    /**
     * Get / verify / create credentials for a user.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function verify($payload = [])
    {
        return $this->create($payload);
    }

    /**
     * Get / verify / create credentials for a user.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function create($payload = [])
    {
        if (! Arr::has($payload, 'reference')) {
            return null;
        }

        return $this->try(function() use ($payload)
        {
            $body = '<?xml version="1.0" encoding="UTF-8"?>
                <credentials>
                    <credential reference="' . Arr::get($payload, 'reference') . '" />
                </credentials>';

            return $this->client->post('v3/credentials', [
                'body' => $body,
            ]);
        });
    }
}
