<?php

namespace Shadowbane\LaravelWablas;

use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\ArrayShape;
use Shadowbane\LaravelWablas\Traits\EndpointTrait;
use Shadowbane\LaravelWablas\Traits\TokenTrait;

/**
 * Class Device.
 *
 * @package Shadowbane\LaravelWablas
 */
class Device
{
    use TokenTrait;
    use EndpointTrait;

    /**
     * Device constructor.
     *
     * @param string|null $token
     * @param string|null $endpoint
     *
     * @throws \Throwable
     */
    public function __construct(string $token = null, string $endpoint = null)
    {
        $this->setToken($token)->setUrl($endpoint);
    }

    /**
     * Get Device Information from Wablas API.
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array
     */
    public function getDeviceInfo(): array
    {
        return Http::get("{$this->url}/device/info?token={$this->token}")
            ->throw()
            ->json();
    }

    /**
     * Get WhatsApp status Summary.
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array
     */
    #[ArrayShape(['quota' => "int", 'expired' => "mixed", 'status' => "string"])]
    public function getWhatsappSummary(): array
    {
        $result = $this->getDeviceInfo();

        return $result['data']['whatsapp'];
    }

    /**
     * Get Whatsapp Status.
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return bool
     */
    public function getWhatsappStatus(): bool
    {
        $whatsappData = $this->getWhatsappSummary();

        return $whatsappData['status'] == 'connected';
    }

    /**
     * Get Remaining Quota.
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return int
     */
    public function getRemainingQuota(): int
    {
        $whatsappData = $this->getWhatsappSummary();

        return $whatsappData['quota'] ?? 0;
    }

}
