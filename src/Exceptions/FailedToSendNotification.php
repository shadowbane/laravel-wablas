<?php

namespace Shadowbane\LaravelWablas\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;

/**
 * Class FailedToSendNotification.
 *
 * @package Shadowbane\LaravelWablas\Exceptions
 */
class FailedToSendNotification extends Exception
{
    /**
     * Thrown when Wablas API Token is not provided / empty.
     *
     * @return static
     */
    public static function tokenIsEmpty(): self
    {
        return new static("Wablas API Token is empty");
    }

    /**
     * Thrown when Wablas API Token is not provided / empty.
     *
     * @return static
     */
    public static function urlIsEmpty(): self
    {
        return new static("Wablas API Endpoint url is empty");
    }

    public static function destinationIsEmpty(): self
    {
        return new static("Destination WhatsApp Number is empty");
    }

    /**
     * Thrown when we could not connect to Wablas API.
     *
     * @return static
     */
    public static function couldNotCommunicateWithWablas(): self
    {
        return new static("Could not communicate with wablas API Server");
    }

    /**
     * Thrown when there's a bad request and an error is responded.
     *
     * @param ClientException $exception
     *
     * @return static
     */
    public static function wablasRespondedWithAnError(ClientException $exception): self
    {
        if (! $exception->hasResponse()) {
            return new static("Wablas responded with an error, but no response body found");
        }

        $statusCode = $exception->getResponse()->getStatusCode();

        $result = json_decode($exception->getResponse()->getBody(), false);
        $description = $result->description ?? 'no description given';

        return new static("Wablas responded with an error `{$statusCode} - {$description}`", 0, $exception);
    }
}
