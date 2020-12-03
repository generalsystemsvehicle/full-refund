<?php

namespace GeneralSystemsVehicle\VitalSource\Api;

use Illuminate\Support\Arr;
use GeneralSystemsVehicle\VitalSource\Guzzle\Api;

class Products extends Api
{
    /**
     * Get the products index.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function index($payload = [])
    {
        return $this->try(function() use ($payload)
        {
            return $this->client->get('v4/products', [
                'query' => Arr::get($payload, 'query', []),
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
        });
    }


    /**
     * Get a product.
     *
     * @param  string $vbid
     * @param  array  $payload
     * @return array|null
     */
    public function get($vbid, $payload = [])
    {
        // Optional query parameter:
        // 'include_details' => 'identifiers,metadata,subjects,accessibility'

        return $this->try(function() use ($vbid, $payload)
        {
            return $this->client->get('v4/products/' . $vbid, [
                'query' => Arr::get($payload, 'query', []),
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
        });
    }

    /**
     * Get the table of contents for a product.
     *
     * @param  string $vbid
     * @return array|null
     */
    public function getTableOfContents($vbid)
    {
        return $this->try(function() use ($vbid)
        {
            return $this->client->get('v4/products/' . $vbid . '/toc', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
        });
    }
}
