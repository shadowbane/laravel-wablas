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
    /** @var string Wablas API Base URI */
    public string $endpoint;

    /**
     * API Base URI setter.
     *
     * @param string|null $endpoint
     *
     * @throws \Throwable
     *
     * @return EndpointTrait|Device|LaravelWablas
     */
    public function setEndpoint(string $endpoint = null): self
    {
        $this->endpoint = !blank($endpoint) ? $endpoint : config('laravel-wablas.endpoint');

        throw_if(blank($this->endpoint), FailedToSendNotification::urlIsEmpty());

        return $this;
    }
}
