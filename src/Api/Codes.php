<?php

namespace GeneralSystemsVehicle\VitalSource\Api;

use Illuminate\Support\Arr;
use GeneralSystemsVehicle\VitalSource\Guzzle\Api;

class Codes extends Api
{
    /**
     * Create a code.
     *
     * @param  array  $payload
     * @return array|null
     */
    public function create($payload = [])
    {
        if (! Arr::has($payload, 'sku')) {
            return null;
        }

        return $this->try(function() use ($payload)
        {
            $body = '<?xml version="1.0" encoding="UTF-8"?>
                <codes
                    sku="' . Arr::get($payload, 'sku') . '"
                    license-type="' . Arr::get($payload, 'license-type', 'perpetual') . '"
                    online-license-type="' . Arr::get($payload, 'online-license-type', 'perpetual') . '"
                    num-codes="1"
                    tag="riverbed_education_created_code"
                />';

            return $this->client->post('v3/codes', [
                'body' => $body,
            ]);
        });
    }

    /**
     * Get a code.
     *
     * @param  string $code
     * @return array|null
     */
    public function get($code)
    {
        // Illuminate\Support\Arr::get($code, 'refundable') == 'true'
        // (int) Illuminate\Support\Arr::get($code, 'refundable_reason_code')
        // 469 - Already cancelled
        // 1005 - Already redeemed and attached online access code already used
        // 1006 - Unassigned code that cannot be cancelled; needs to be deactivated
        // 1007 - Already redeemed and past 14 day trial
        // 1014 - User accessed too many pages

        return $this->try(function() use ($code)
        {
            return $this->client->get('v4/codes/' . $code);
        });
    }

    /**
     * Delete / refund / cancel a code.
     *
     * @param  string $code
     * @return array|null
     */
    public function refund($code)
    {
        return $this->delete($code);
    }

    /**
     * Delete / refund / cancel a code.
     *
     * @param  string $code
     * @return array|null
     */
    public function cancel($code)
    {
        return $this->delete($code);
    }

    /**
     * Delete / refund / cancel a code.
     *
     * @param  string $code
     * @return array|null
     */
    public function delete($code)
    {
        return $this->try(function() use ($code)
        {
            return $this->client->delete('v4/codes/' . $code);
        });
    }
}
