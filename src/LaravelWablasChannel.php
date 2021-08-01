<?php

namespace Shadowbane\LaravelWablas;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Shadowbane\LaravelWablas\Exceptions\FailedToSendNotification;

/**
 * Class LaravelWablasChannel.
 *
 * @package Shadowbane\LaravelWablas
 */
class LaravelWablasChannel
{
    /**
     * @var LaravelWablas
     */
    protected $wablas;

    public function __construct(LaravelWablas $wablas)
    {
        $this->wablas = $wablas;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @throws FailedToSendNotification
     * @return null|array
     */
    public function send($notifiable, Notification $notification): ?array
    {
        $message = $notification->toWhatsapp($notifiable);

        if (is_string($message)) {
            $message = LaravelWablasMessage::create($message);
        }

        if ($notifiable instanceof User) {
            $waNumber = $this->getNotifiableWhatsappNumber($notifiable);
            if (!blank($waNumber)) {
                $message->to($waNumber);
            }
        }

        $params = $message->toArray();

        if (blank($params['phone'])) {
            throw FailedToSendNotification::destinationIsEmpty();
        }

        if (isset($params['token']) && !empty($params['token'])) {
            $this->wablas->setToken($params['token']);
            unset ($params['token']);
        }

        $response = $this->wablas->sendMessage($params);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param User $user
     *
     * @throws FailedToSendNotification
     *
     * @return string|null
     */
    private function getNotifiableWhatsappNumber(User $user): ?string
    {
        // return debug phone number if local
        // this will prevent real user getting debug notification
        if (app()->isLocal() && config('app.debug')) {
            return config('laravel-wablas.debug_number');
        }

        $whatsapp = null;

        $waField = $user->{config('laravel-wablas.whatsapp_number_field')};

        if (!blank(config('laravel-wablas.whatsapp_number_json_field')) && !blank($waField)) {
            $whatsapp = $waField[config('laravel-wablas.whatsapp_number_json_field')];
        }

        if (!blank($whatsapp)) {
            if (substr($whatsapp, 0, 1) == '0') {
                $whatsapp = substr($whatsapp, 1, strlen($whatsapp) - 1);
            }

            $whatsapp = 62 . $whatsapp;
        }

        return $whatsapp;
    }
}
