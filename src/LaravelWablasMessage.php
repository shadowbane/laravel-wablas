<?php

namespace Shadowbane\LaravelWablas;

use JsonSerializable;
use Shadowbane\LaravelWablas\Exceptions\FailedToSendNotification;
use Shadowbane\LaravelWablas\Exceptions\LaravelWablasException;

/**
 * Class LaravelWablasMessage.
 *
 * @package Shadowbane\LaravelWablas
 */
class LaravelWablasMessage implements JsonSerializable
{
    public string $token = '';

    public string $phone;
    public string $message;
    public string|null $attachment = null;

    public string $type = 'message';
    public bool $secret = false;
    public bool $retry = false;
    public bool $isGroup = false;

    /**
     * Message constructor.
     *
     * @param string $content
     * @param string|null $attachment
     */
    public function __construct(string $content = '', string $attachment = null)
    {
        $this->content(
            content: $content,
            attachment: $attachment
        );
    }

    /**
     * @param string $content
     * @param string|null $attachment
     *
     * @return static
     */
    public static function create(string $content = '', string $attachment = null): self
    {
        return new self(
            content: $content,
            attachment: $attachment
        );
    }

    /**
     * Notification message (Supports Markdown).
     *
     * @param string $content
     * @param string|null $attachment
     *
     * @return $this
     */
    public function content(string $content = '', string $attachment = null): self
    {
        $this->message = $content;
        $this->attachment = $attachment;

        return $this;
    }

    /**
     * Notification Attachment (For image, video, document, or audio message).
     *
     * @param string $attachment
     *
     * @return $this
     */
    public function attachment(string $attachment): self
    {
        $this->attachment = $attachment;

        return $this;
    }

    /**
     * Recipient's Phone number.
     *
     * @param $phoneNumber
     *
     * @return $this
     * @throws FailedToSendNotification
     */
    public function to($phoneNumber): self
    {
        // return debug phone number if local
        // this will prevent real user getting debug notification
        if (app()->isLocal() && config('app.debug')) {
            $this->phone = config('laravel-wablas.debug_number');

            return $this;
        }

        // throw error if $phoneNumber is blank
        if (blank($phoneNumber)) {
            throw FailedToSendNotification::destinationIsEmpty();
        }

        $this->phone = $phoneNumber;

        return $this;
    }

    /**
     * Set Wablas Token.
     * Useful when you want to send with another token.
     *
     * @param $token
     *
     * @return $this
     * @throws FailedToSendNotification
     */
    public function token($token): self
    {
        if (blank($token)) {
            throw FailedToSendNotification::tokenIsEmpty();
        }

        $this->token = $token;

        return $this;
    }

    /**
     * @throws \Shadowbane\LaravelWablas\Exceptions\LaravelWablasException
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Returns params payload.
     *
     * @throws \Shadowbane\LaravelWablas\Exceptions\LaravelWablasException
     *
     * @return array
     */
    public function toArray(): array
    {
        $arr = [
            'token' => $this->token,
            'phone' => $this->phone ?? null,
            'secret' => $this->secret,
            'retry' => $this->retry,
            'isGroup' => $this->isGroup,
        ];

        if (in_array($this->type, ['image', 'video', 'document'])) {
            if (blank($this->attachment)) {
                throw LaravelWablasException::attachmentIsEmpty();
            }
            $arr['caption'] = $this->message;
            $arr[$this->type] = $this->attachment;
        }

        if ($this->type === 'audio') {
            $arr[$this->type] = $this->attachment;
        }

        if ($this->type === 'message') {
            $arr['message'] = $this->message;
        }

        return $arr;
    }

    /**
     * Send message as Text.
     *
     * @throws \Shadowbane\LaravelWablas\Exceptions\LaravelWablasException
     *
     * @return $this
     */
    public function sendAsText(): self
    {
        return $this->setType('message');
    }

    /**
     * Send message as Image.
     *
     * @throws \Shadowbane\LaravelWablas\Exceptions\LaravelWablasException
     *
     * @return $this
     */
    public function sendAsImage(): self
    {
        return $this->setType('image');
    }

    /**
     * Send message as Audio.
     *
     * @throws \Shadowbane\LaravelWablas\Exceptions\LaravelWablasException
     *
     * @return $this
     */
    public function sendAsAudio(): self
    {
        return $this->setType('audio');
    }

    /**
     * Send message as Video.
     *
     * @throws \Shadowbane\LaravelWablas\Exceptions\LaravelWablasException
     *
     * @return $this
     */
    public function sendAsVideo(): self
    {
        return $this->setType('video');
    }

    /**
     * Send message as Document.
     *
     * @throws \Shadowbane\LaravelWablas\Exceptions\LaravelWablasException
     *
     * @return $this
     */
    public function sendAsDocument(): self
    {
        return $this->setType('document');
    }

    /**
     * Set Message Type.
     *
     * @param string $type
     *
     * @throws \Shadowbane\LaravelWablas\Exceptions\LaravelWablasException
     *
     * @return $this
     */
    public function setType(string $type): self
    {
        // validate the type. allowed type is: message (default), image, audio, video, document
        if (!in_array($type, ['message', 'image', 'audio', 'video', 'document'])) {
            throw LaravelWablasException::invalidMessageType();
        }

        $this->type = $type;

        return $this;
    }
}
