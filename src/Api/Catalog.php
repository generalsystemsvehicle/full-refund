<?php

namespace GeneralSystemsVehicle\VitalSource\Api;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use GuzzleHttp\Exception\RequestException;
use GeneralSystemsVehicle\VitalSource\Guzzle\Api;

class Catalog extends Api
{
    /**
     * Get the catalog index.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function index($payload = [])
    {
        return $this->try(function() use ($payload)
        {
            $body = '<?xml version="1.0" encoding="UTF-8"?>
                <catalog>
                    <page>' . Arr::get($payload, 'page', 0) . '</page>
                    <per-page>' . Arr::get($payload, 'per-page', 1000) . '</per-page>
                </catalog>';

            return $this->client->post('v3/catalog', [
                'body' => $body,
            ]);
        });
    }
}
