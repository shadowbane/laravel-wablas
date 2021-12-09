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
     *
     * @return null|array
     */
    public function send($notifiable, Notification $notification): ?array
    {
        $message = $notification->toWhatsapp($notifiable);

        if (is_string($message)) {
            $message = LaravelWablasMessage::create($message);
        }

        $params = $message->toArray();

        if (blank($params['phone'])) {

            if ($notifiable instanceof User) {
                $waNumber = $this->getNotifiableWhatsappNumber($notifiable);
                if (!blank($waNumber)) {
                    $message->to($waNumber);
                }
            }

            $params = $message->toArray();

            throw_if(blank($params['phone']), FailedToSendNotification::destinationIsEmpty());
        }

        if (isset($params['token'])) {
            if (!blank($params['token'])) {
                $this->wablas->setToken($params['token']);
            }

            unset ($params['token']);
        }

        match (true) {
            isset($params['image']) => $this->wablas->setEndpoint('image'),
            isset($params['audio']) => $this->wablas->setEndpoint('audio'),
            isset($params['video']) => $this->wablas->setEndpoint('video'),
            isset($params['document']) => $this->wablas->setEndpoint('document'),
            default => $this->wablas->setEndpoint('message'),
        };

        return $this->wablas->sendMessage($params);
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

        return $whatsapp;
    }
}
