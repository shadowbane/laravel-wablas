<?php

namespace Shadowbane\LaravelWablas;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
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

    /** @var HttpClient HTTP Client */
    protected HttpClient $http;

    /** @var Device Wablas Device API */
    public Device $device;

    /**
     * @param string|null $token
     * @param HttpClient|null $httpClient
     * @param string|null $endpoint
     *
     * @throws FailedToSendNotification
     * @throws \Throwable
     */
    public function __construct(string $token = null, HttpClient $httpClient = null, string $endpoint = null)
    {
        $this->http = $httpClient ?? new HttpClient();

        $this
            ->setToken($token)
            ->setEndpoint($endpoint);

        $this->device = new Device($this->token, $this->endpoint);
    }

    /**
     * Get HttpClient.
     *
     * @return HttpClient
     */
    protected function httpClient(): HttpClient
    {
        return $this->http;
    }

    /**
     * Set HTTP Client.
     *
     * @param HttpClient $http
     *
     * @return $this
     */
    public function setHttpClient(HttpClient $http): self
    {
        $this->http = $http;

        return $this;
    }

    /**
     * @param array $params
     * @return ResponseInterface|null
     *
     * @throws FailedToSendNotification
     */
    public function sendMessage(array $params): ?ResponseInterface
    {
        return $this->sendRequest($params);
    }

    /**
     * Send an API request and return response.
     *
     * @param array  $params
     *
     * @throws FailedToSendNotification|\GuzzleHttp\Exception\GuzzleException
     *
     * @return ResponseInterface|null
     */
    protected function sendRequest(array $params): ?ResponseInterface
    {
        if (blank($this->token)) {
            throw FailedToSendNotification::tokenIsEmpty();
        }

        try {
            return $this->http->post("{$this->endpoint}/send-message", [
                'headers' => [
                    'Authorization' => $this->token,
                ],
                'json' => $params,
            ]);
        } catch (ClientException $exception) {
            throw FailedToSendNotification::wablasRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw FailedToSendNotification::couldNotCommunicateWithWablas();
        }
    }
}
