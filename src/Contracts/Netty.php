<?php

namespace Krish033\Otpfy\Contracts;

interface Netty
{
    /*
    |--------------------------------------------------------------------------
    | Get Template Identifier
    |--------------------------------------------------------------------------
    |
    | This method should return the unique template ID registered with the
    | NettyFish SMS provider. The template ID is used to match the message
    | body with an approved DLT template.
    |
    | Example:
    |   return 'WELCOME_TEMPLATE_001';
    |
    | @return string
    |
    */
    public function template(): string;

    /*
    |--------------------------------------------------------------------------
    | Get Message Content
    |--------------------------------------------------------------------------
    |
    | This method should return the actual message content associated with
    | the template. Dynamic values may be injected using placeholders that
    | the NettyFish provider supports (e.g., {{name}}, {{otp}}, etc).
    |
    | Example:
    |   return 'Welcome {{name}}, your OTP is {{otp}}.';
    |
    | @return string
    |
    */
    public function message(): string;
}
