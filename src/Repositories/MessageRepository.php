<?php

namespace Krish033\Otpfy\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Krish033\Otpfy\Contracts\Message;
use Krish033\Otpfy\Contracts\Netty;

/*
|--------------------------------------------------------------------------
| Message Repository
|--------------------------------------------------------------------------
|
| This class is the concrete implementation of the Message contract.
| It is responsible for preparing, validating, and dispatching messages
| through the NettyFish SMS Gateway (or any other configured provider).
|
| Usage Example:
|   Message::send(new WelcomeTemplate())->to('+919876543210');
|
*/

class MessageRepository implements Message
{
    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    /**
     * The message template to be sent.
     *
     * @var \Krish033\Otpfy\Contracts\Netty
     */
    protected $message;

    /**
     * The recipients of the message.
     *
     * @var array
     */
    protected $to = [];


    /*
    |--------------------------------------------------------------------------
    | Contract Implementations
    |--------------------------------------------------------------------------
    */

    /**
     * Assign the message template instance.
     *
     * @param  \Krish033\Otpfy\Contracts\Netty  $message
     * @return $this
     */
    public function send(Netty $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Dispatch the prepared message to one or more recipients.
     *
     * @param  string  ...$recipient
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Exception
     */
    public function to(string ...$recipient)
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | Validate Recipients
            |--------------------------------------------------------------------------
            |
            | Basic validation ensures that at least one recipient phone number
            | is provided. You can expand this to include regex patterns for
            | stricter phone number validation if needed.
            |
            */
            $validator = Validator::make(
                ['phone' => $recipient],
                ['phone' => ['required']]
            );

            if ($validator->fails()) {
                throw new \Exception("Phone number is required to send message.");
            }

            // Store recipients
            $this->to = $recipient;

            /*
            |--------------------------------------------------------------------------
            | Dispatch Message via HTTP
            |--------------------------------------------------------------------------
            |
            | Uses Laravel's HTTP Client to send a POST request to the NettyFish
            | API endpoint, using configuration values from config/message.php.
            |
            */
            return Http::post(config('message.NettyFishUrl'), [
                "Account" => [
                    "APIKey"   => config("message.APIKey"),
                    "SenderId" => config("message.SenderId"),
                    "Channel"  => config("message.Channel"),
                    "DCS"      => config("message.DCS"),
                    "FlashSms" => config("message.FlashSms"),
                    "Route"    => config("message.Route"),
                    "PeId"     => config("message.PeId"),
                ],
                "Messages" => $this->people($this->to),
            ]);
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Format recipients into the NettyFish API structure.
     *
     * @param  array  $numbers
     * @return array
     */
    protected function people(array $numbers): array
    {
        return collect($numbers)->map(function ($number) {
            return [
                "Number"        => str($number),
                "dlttemplateid" => $this->message->template(),
                "Text"          => $this->message->message(),
            ];
        })->toArray();
    }
}
