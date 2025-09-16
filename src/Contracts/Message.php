<?php

namespace Krish033\Otpfy\Contracts;

interface Message
{
    /*
    |--------------------------------------------------------------------------
    | Send Message Using Template
    |--------------------------------------------------------------------------
    |
    | This method is responsible for preparing and dispatching the message
    | using a given Netty template. The Netty contract defines both the
    | message template ID and the body of the message.
    |
    | Example usage:
    |   $message->send(new WelcomeTemplate());
    |
    | @param \Krish033\Otpfy\Contracts\Netty $message
    | @return mixed
    |
    */
    public function send(Netty $message);

    /*
    |--------------------------------------------------------------------------
    | Set Message Recipient(s)
    |--------------------------------------------------------------------------
    |
    | This method defines one or more recipients for the outgoing message.
    | You may pass multiple phone numbers as arguments. The method should
    | return an instance of the message pipeline to allow chaining.
    |
    | Example usage:
    |   $message->to('9876543210')
    |           ->send(new WelcomeTemplate());
    |
    | @param string ...$recipient
    | @throws \Exception
    | @return \Illuminate\Http\Client\Response|self
    |
    */
    public function to(string ...$recipient);
}
