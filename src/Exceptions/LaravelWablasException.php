<?php

namespace Shadowbane\LaravelWablas\Exceptions;

class LaravelWablasException extends \Exception
{
    /**
     * Thrown when Wablas API Token is not provided / empty.
     *
     * @return static
     */
    public static function invalidMessageType(): self
    {
        return new static('Invalid message type.');
    }

    /**
     * Thrown when Wablas API Token is not provided / empty.
     *
     * @return static
     */
    public static function attachmentIsEmpty(): self
    {
        return new static('Attachment is Empty.');
    }
}