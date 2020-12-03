<?php

namespace GeneralSystemsVehicle\VitalSource\Api;

use Illuminate\Support\Arr;
use GeneralSystemsVehicle\VitalSource\Guzzle\Api;

class Redirects extends Api
{
    /**
     * Generate an SSO link to a book for a user.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function sso($payload = [])
    {
        if (! Arr::has($payload, 'X-VitalSource-Access-Token')) {
            return null;
        }

        if (! Arr::has($payload, 'destination')) {
            return null;
        }

        // Destination - https://bookshelf.vitalsource.com/books/{vbid}
        // Brand - bookshelf.vitalsource.com

        return $this->try(function() use ($payload)
        {
            $body = '<?xml version="1.0" encoding="UTF-8"?>
                <redirect>
                    <destination>' . Arr::get($payload, 'destination') . '</destination>
                    <brand>' . Arr::get($payload, 'brand', 'bookshelf.vitalsource.com') . '</brand>
                </redirect>';

            return $this->client->post('v3/redirects', [
                'body' => $body,
                'headers' => [
                    'X-VitalSource-Access-Token' => Arr::get($payload, 'X-VitalSource-Access-Token'),
                ],
            ]);
        });
    }
}
