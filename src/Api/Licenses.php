<?php

namespace GeneralSystemsVehicle\VitalSource\Api;

use GeneralSystemsVehicle\VitalSource\Guzzle\Api;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;

class Licenses extends Api
{
    /**
     * Gets licenses for a user.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function get($payload = [])
    {
        if (! Arr::has($payload, 'X-VitalSource-Access-Token')) {
            return null;
        }

        return $this->try(function() use ($payload)
        {
            return $this->client->get('v3/licenses', [
                'headers' => [
                    'X-VitalSource-Access-Token' => Arr::get($payload, 'X-VitalSource-Access-Token'),
                ],
            ]);
        });
    }
}
