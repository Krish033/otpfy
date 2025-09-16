<?php

return [

    /*
    |--------------------------------------------------------------------------
    | NettyFish API URL
    |--------------------------------------------------------------------------
    |
    | This value is the base URL for the NettyFish SMS gateway.
    | You can set it in your .env file as NETTYFISH_URL.
    | Example: https://api.nettyfish.com
    |
    */
    "NettyFishUrl" => env("NETTYFISH_URL", null),

    /*
    |--------------------------------------------------------------------------
    | NettyFish API Key
    |--------------------------------------------------------------------------
    |
    | This is your authentication key for accessing the NettyFish service.
    | Place the API key provided by NettyFish in your .env file as NETTYFISH_API_KEY.
    |
    */
    "APIKey" => env("NETTYFISH_API_KEY", null),

    /*
    |--------------------------------------------------------------------------
    | Sender ID
    |--------------------------------------------------------------------------
    |
    | This is the registered sender ID used to send messages.
    | Example: NETTYFISH, OTPMSG, etc.
    | Set it in your .env file as NETTYFISH_SENDER_ID.
    |
    */
    "SenderId" => env("NETTYFISH_SENDER_ID", null),

    /*
    |--------------------------------------------------------------------------
    | Sender Channel
    |--------------------------------------------------------------------------
    |
    | The channel through which the SMS will be sent.
    | Common values: SMS, WAP, etc.
    | Configure in your .env file as NETTYFISH_SENDER_CHANNEL.
    |
    */
    "Channel" => env("NETTYFISH_SENDER_CHANNEL", null),

    /*
    |--------------------------------------------------------------------------
    | Data Coding Scheme (DCS)
    |--------------------------------------------------------------------------
    |
    | This represents the type of message encoding.
    | For example: 0 = English, 8 = Unicode.
    | Configure in your .env file as NETTYFISH_SENDER_DCS.
    |
    */
    "DCS" => env("NETTYFISH_SENDER_DCS", null),

    /*
    |--------------------------------------------------------------------------
    | Flash SMS
    |--------------------------------------------------------------------------
    |
    | Indicates whether the SMS is a Flash message.
    | Use 0 for normal SMS, 1 for Flash SMS.
    | Configure in your .env file as NETTYFISH_FLASH_SMS.
    |
    */
    "FlashSms" => env("NETTYFISH_FLASH_SMS", null),

    /*
    |--------------------------------------------------------------------------
    | Route
    |--------------------------------------------------------------------------
    |
    | The route parameter determines the delivery route of the SMS.
    | Example: 1 for default route.
    | Configure in your .env file as NETTYFISH_SENDER_ROUTE.
    |
    */
    "Route" => env("NETTYFISH_SENDER_ROUTE", null),

    /*
    |--------------------------------------------------------------------------
    | PEID (Principal Entity ID)
    |--------------------------------------------------------------------------
    |
    | The PEID is required for DLT (Distributed Ledger Technology) compliance
    | in India. You can obtain this from your SMS provider.
    | Configure in your .env file as NETTYFISH_PEID.
    |
    */
    "PeId" => env("NETTYFISH_PEID", null),

];
