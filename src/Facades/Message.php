<?php

namespace Krish033\Otpfy\Facades;

use Illuminate\Support\Facades\Facade;

/*
|--------------------------------------------------------------------------
| Message Facade
|--------------------------------------------------------------------------
|
| This Facade provides a simple and expressive interface to interact with
| the underlying "message" service within the Otpfy package. Instead of
| resolving the service out of the container manually, you may call
| methods statically using the Facade, keeping your code clean.
|
| Example Usage:
|   Message::send($nettyInstance)->to('+919876543210');
|
*/

class Message extends Facade
{
    /*
    |--------------------------------------------------------------------------
    | Get Facade Accessor
    |--------------------------------------------------------------------------
    |
    | This method returns the service container binding key that this Facade
    | will resolve. In this case, the key is "message", which should be
    | bound inside your package's service provider.
    |
    | @return string
    |
    */
    protected static function getFacadeAccessor(): string
    {
        return 'message';
    }

    /*
    |--------------------------------------------------------------------------
    | Magic Static Proxy Methods (IDE Helper)
    |--------------------------------------------------------------------------
    |
    | While Facades resolve methods at runtime, documenting available methods
    | here helps IDEs with autocomplete. You can expand this section as your
    | message service grows.
    |
    | Available Methods:
    |   @method static \Krish033\Otpfy\Contracts\Message send(\Krish033\Otpfy\Contracts\Netty $message)
    |   @method static \Illuminate\Http\Client\Response to(string ...$recipient)
    |
    */
}
