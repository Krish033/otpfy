<?php

namespace Krish033\Otpfy;

use Illuminate\Support\ServiceProvider;
use Krish033\Otpfy\Contracts\Auth;
use Krish033\Otpfy\Repositories\AuthRepository;
use Krish033\Otpfy\Contracts\Message;
use Krish033\Otpfy\Repositories\MessageRepository;

class MessageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->mergeConfigFrom(__DIR__ . '/../config/message.php', 'message');

        $this->app->singleton(Message::class, function ($app) {
            return new MessageRepository(config('message'));
        });

        $this->app->singleton(Auth::class, function ($app) {
            return new AuthRepository(config('message'));
        });


        $this->app->alias(Message::class, 'message');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


        if ($this->app->runningInConsole()) {
            $this->commands([
                \Krish033\Otpfy\Console\MessageCommand::class,
            ]);
        }


        if ($this->app->runningInConsole()) {
            $this->commands([
                \Krish033\Otpfy\Console\OtpfyInstallCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/message.php' => config_path('message.php'),
        ], 'message-config');


        $this->publishes([
            __DIR__ . '/Traits/JwtAuthenticatable.php' => app_path('Traits/JwtAuthenticatable.php'),
        ], 'message-jwt');
    }
}
