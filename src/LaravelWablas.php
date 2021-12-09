<?php

namespace Shadowbane\LaravelWablas;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Http;
use Shadowbane\LaravelWablas\Exceptions\FailedToSendNotification;
use Shadowbane\LaravelWablas\Traits\EndpointTrait;
use Shadowbane\LaravelWablas\Traits\TokenTrait;

/**
 * Class LaravelWablas.
 *
 * @package Shadowbane\LaravelWablas
 */
class LaravelWablas
{
    use EndpointTrait;
    use TokenTrait;

    /** @var Device Wablas Device API */
    public Device $device;

    /**
     * @param string|null $token
     * @param string|null $endpoint
     *
     * @throws FailedToSendNotification
     * @throws \Throwable
     */
    public function __construct(string $token = null, string $endpoint = null)
    {
        $this
            ->setToken($token)
            ->setUrl($endpoint);

        $this->device = new Device($this->token, $this->url);
    }

    /**
     * Send an API request and return response.
     *
     * @param array  $params
     *
     * @throws FailedToSendNotification
     *
     * @return ?array
     */
    public function sendMessage(array $params): ?array
    {
        if (blank($this->token)) {
            throw FailedToSendNotification::tokenIsEmpty();
        }

        try {
            return Http::withHeaders([
                'Authorization' => $this->token,
            ])
                ->acceptJson()
                ->post($this->getEndpoint(), [
                    'data' => [
                        $params,
                    ],
                ])
                ->throw(function ($response, $exception) {
                    dd($exception->getMessage());
                })
                ->json();
        } catch (ClientException $exception) {
            throw FailedToSendNotification::wablasRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw FailedToSendNotification::couldNotCommunicateWithWablas();
        }
    }
}
