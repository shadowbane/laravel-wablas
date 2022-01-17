<?php

namespace Shadowbane\LaravelWablas\Traits;

use Shadowbane\LaravelWablas\Exceptions\FailedToSendNotification;
use Shadowbane\LaravelWablas\LaravelWablas;

/**
 * Trait TokenTrait.
 *
 * @package Shadowbane\LaravelWablas\Traits
 */
trait TokenTrait
{
    /** @var string Wablas API Token. */
    public string $token;

    /**
     * @param string|null $token
     *
     * @throws \Throwable
     *
     * @return TokenTrait|LaravelWablas
     */
    public function setToken(string $token = null): self
    {
        $this->token = !blank($token) ? $token : config('laravel-wablas.token');

        return $this;
    }
}
