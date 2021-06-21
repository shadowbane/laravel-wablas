<?php

namespace Shadowbane\LaravelWablas;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Shadowbane\LaravelWablas\Exceptions\FailedToSendNotification;

/**
 * Class LaravelWablas.
 *
 * @package Shadowbane\LaravelWablas
 */
class LaravelWablas
{
    /** @var HttpClient HTTP Client */
    protected $http;

    /** @var string|null Telegram Bot API Token. */
    protected $token;

    /** @var string Telegram Bot API Base URI */
    protected $apiBaseUri;

    /**
     * @param string|null $token
     * @param HttpClient|null $httpClient
     * @param string|null $apiBaseUri
     *
     * @throws FailedToSendNotification
     *
     */
    public function __construct(string $token = null, HttpClient $httpClient = null, string $apiBaseUri = null)
    {
        $this->token = $token;
        $this->http = $httpClient ?? new HttpClient();
        $this->setApiBaseUri($apiBaseUri);
    }

    /**
     * API Base URI setter.
     *
     * @param string|null $apiBaseUri
     *
     * @throws FailedToSendNotification
     *
     * @return $this
     */
    public function setApiBaseUri(?string $apiBaseUri): self
    {
        if (empty($apiBaseUri) || is_null($apiBaseUri)) {
            throw FailedToSendNotification::tokenIsEmpty();
        }

        $this->apiBaseUri = rtrim($apiBaseUri, '/');

        return $this;
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
     * Set Token.
     *
     * @param string $token
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

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
     * @throws FailedToSendNotification
     *
     * @return ResponseInterface|null
     */
    protected function sendRequest(array $params): ?ResponseInterface
    {
        if (blank($this->token)) {
            throw FailedToSendNotification::tokenIsEmpty();
        }

        try {
            return $this->http->post($this->apiBaseUri, [
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
