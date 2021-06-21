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
     * @param mixed        $notifiable
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
            $message->to($this->getNotifiableWhatsappNumber($notifiable));
        }

        $params = $message->toArray();

        if (isset($params['token']) && !empty($params['token'])) {
            $this->wablas->setToken($params['token']);
            unset ($params['token']);
        }

        $response = $this->wablas->sendMessage($params);

        return json_decode($response->getBody()->getContents(), true);
    }

    private function getNotifiableWhatsappNumber(User $user)
    {
        $whatsapp = $user->{config('laravel-wablas.whatsapp_number_field')};

        if (!blank(config('laravel-wablas.whatsapp_number_json_field'))) {
            $whatsapp = $whatsapp[config('laravel-wablas.whatsapp_number_json_field')];
        }

        if (substr($whatsapp, 0, 1) == '0' || substr($whatsapp, 0, 1) == '8') {
            $whatsapp = 62 . $whatsapp;
        }

        return $whatsapp;
    }
}
