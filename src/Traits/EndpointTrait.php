<?php

namespace Shadowbane\LaravelWablas\Traits;

use Shadowbane\LaravelWablas\Device;
use Shadowbane\LaravelWablas\Exceptions\FailedToSendNotification;
use Shadowbane\LaravelWablas\LaravelWablas;

/**
 * Trait EndpointTrait.
 *
 * @package Shadowbane\LaravelWablas\Traits
 */
trait EndpointTrait
{
    /** @var string */
    public string $url = '';
    /** @var string */
    public string $endpoint = '';

    /**
     * API Base URI setter.
     *
     * @param string|null $endpoint
     *
     * @throws \Throwable
     *
     * @return EndpointTrait|Device|LaravelWablas
     */
    public function setUrl(string $endpoint = null): self
    {
        $this->url = !blank($endpoint) ? $endpoint : config('laravel-wablas.endpoint');

        throw_if(blank($this->url), FailedToSendNotification::urlIsEmpty());

        return $this;
    }

    /**
     * API Endpoint setter.
     *
     * @param string|null $endpoint
     *
     * @throws FailedToSendNotification
     *
     * @return EndpointTrait|Device|LaravelWablas
     */
    public function setEndpoint(string $endpoint = null): self
    {
        if (blank($this->url) || blank($endpoint)) {
            throw FailedToSendNotification::urlIsEmpty();
        }

        $endpoint = match ($endpoint) {
            'message' => 'send-message',
            'image' => 'send-image',
            'audio' => 'send-audio',
            'video' => 'send-video',
            'document' => 'send-document',
        };

        $this->endpoint = rtrim($this->url, '/').'/api/v2/'.$endpoint;

        return $this;
    }

    /**
     * @param string $endpoint
     *
     * @throws \Shadowbane\LaravelWablas\Exceptions\FailedToSendNotification
     *
     * @return string
     */
    public function getEndpoint(string $endpoint = ''): string
    {
        if (blank($this->endpoint)) {
            $this->setEndpoint($endpoint);
        }

        return $this->endpoint;
    }
}
