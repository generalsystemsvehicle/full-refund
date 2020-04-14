<?php

namespace GeneralSystemsVehicle\VitalSource\Api;

use Illuminate\Support\Arr;
use GuzzleHttp\Exception\RequestException;
use GeneralSystemsVehicle\VitalSource\Guzzle\Api;

class Redemptions extends Api
{
    /**
     * Redeem / create a code.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function redeem($payload = [])
    {
        return $this->create($payload);
    }

    /**
     * Redeem / create a code.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function create($payload = [])
    {
        if (! Arr::has($payload, 'X-VitalSource-Access-Token')) {
            return null;
        }

        if (! Arr::has($payload, 'code')) {
            return null;
        }

        return $this->try(function() use ($payload)
        {
            $body = '<?xml version="1.0" encoding="UTF-8"?>
                <redemption>
                    <code>' . Arr::get($payload, 'code') . '</code>
                </redemption>';

            return $this->client->post('v3/redemptions', [
                'body' => $body,
                'headers' => [
                    'X-VitalSource-Access-Token' => Arr::get($payload, 'X-VitalSource-Access-Token'),
                ],
            ]);
        });
    }
}
