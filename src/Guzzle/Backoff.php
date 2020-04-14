<?php

namespace GeneralSystemsVehicle\VitalSource\Guzzle;

use Carbon\Carbon;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Backoff
{
    /**
     * @var boolean
     */
    public static $isDelayEnabled = true;

    /**
     * Decide if Guzzle should retry a request
     *
     * @return \Closure
     */
    public static function decider()
    {
        return function ($retries, Request $request, Response $response = null, RequestException $exception = null) {
            // Limit the number of retries to 5
            if ($retries >= 5) {
                return false;
            }

            // Retry connection exceptions
            if ($exception instanceof ConnectException) {
                return true;
            }

            $exceptionStatusCode = optional(optional($exception)->getResponse())->getStatusCode();

            // Retry on rate limiting
            if ($exceptionStatusCode == 429) {
                return true;
            }

            // Retry on server errors
            if ($exceptionStatusCode >= 500) {
                return true;
            }

            return false;
        };
    }

    /**
     * Determine the amount of time to delay before retrying a request.
     *
     * @return \Closure
     */
    public static function delay()
    {
        if (! static::$isDelayEnabled) {
            return function ($retries, Response $response = null) {
                return 0;
            };
        }

        return function ($retries, Response $response = null) {
            $startOfMinute = 0;

            if ($response && $response->getStatusCode() == 429) {
                $startOfMinute = Carbon::now()->diffInMilliseconds(
                    Carbon::now()->addMinute()->startOfMinute()
                );
            }

            return ((int) pow(2, $retries - 1) * 1000) + $startOfMinute;
        };
    }
}
