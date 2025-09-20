<?php

namespace Krish033\Otpfy\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OtpfyInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan otpfy:install
     */
    protected $signature = 'otpfy:install';

    /**
     * The console command description.
     */
    protected $description = 'Install Otpfy package: publish routes, controllers, and create a default template.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Installing Otpfy...');

        // Publish route file
        $this->publishFile(
            __DIR__ . '/../../routes/otpfy.php',
            base_path('routes/otpfy.php'),
            'Route file'
        );

        // Publish controller(s)
        $this->publishFile(
            __DIR__ . '/../Http/Controllers/Auth/AuthenticatedSessionController.php',
            app_path('Http/Controllers/Auth/AuthenticatedSessionController.php'),
            'AuthenticatedSessionController'
        );

        $this->publishFile(
            __DIR__ . '/../Http/Controllers/Auth/RegisterUserController.php',
            app_path('Http/Controllers/Auth/RegisterUserController.php'),
            'RegisterUserController'
        );

        $this->publishFile(
            __DIR__ . '/../Http/Controllers/Auth/VerifyAuthenticatedController.php',
            app_path('Http/Controllers/Auth/VerifyAuthenticatedController.php'),
            'VerifyAuthenticatedController'
        );

        $this->publishFile(
            __DIR__ . '/../Http/Middleware/RequestInterseptor.php',
            app_path('Http/Middleware/RequestInterseptor.php'),
            'RequestInterseptor'
        );


        // 👇 Add this
        $this->updateApiRoutesFile();

        $userModel = app_path('Models/User.php');

        if (! file_exists($userModel)) {
            $this->error("User model not found at {$userModel}");
            return Command::FAILURE;
        }

        $content = file_get_contents($userModel);

        // 1. Ensure the trait `use` line is present
        if (! str_contains($content, 'use Tymon\\JWTAuth\\Contracts\\JWTSubject;')) {
            $content = preg_replace(
                '/namespace\s+App\\\Models;(\s+)/',
                "namespace App\Models;\n\nuse use Tymon\\JWTAuth\\Contracts\\JWTSubject;$1",
                $content,
                1
            );
        }


        // 1. Ensure the trait `use` line is present
        if (! str_contains($content, 'use Krish033\\Otpfy\\Traits\\JwtAuthenticatable;')) {
            $content = preg_replace(
                '/namespace\s+App\\\Models;(\s+)/',
                "namespace App\Models;\n\nuse Krish033\\Otpfy\\Traits\\JwtAuthenticatable;$1",
                $content,
                1
            );
        }


        // 2. Ensure the trait is used inside the class
        if (! str_contains($content, 'use JwtAuthenticatable;')) {
            $content = preg_replace(
                '/class\s+User\s+extends\s+[^{]+{/',
                "class User extends Authenticatable\n{\n    use JwtAuthenticatable;\n",
                $content,
                1
            );
        }


        // 2. Ensure the trait is used inside the class
        if (! str_contains($content, 'implements JWTSubject')) {
            $content = preg_replace(
                '/class\s+User\s+extends\s+[^{]+{/',
                "class User extends Authenticatable implements JWTSubject\n{\n",
                $content,
                1
            );
        }

        file_put_contents($userModel, $content);

        $configFile = config_path('auth.php');
        $content = file_get_contents($configFile);

        // Check if "api" guard already exists
        if (! str_contains($content, "'api' => [")) {
            $content = preg_replace(
                "/'guards'\s*=>\s*\[/",
                "'guards' => [\n\n        'api' => [\n            'driver' => 'jwt',\n            'provider' => 'users',\n        ],\n",
                $content,
                1
            );

            file_put_contents($configFile, $content);

            $this->info('✅ JWT "api" guard added to auth.php.');
        } else {
            $this->warn('⚠️  "api" guard already exists in auth.php, skipped.');
        }

        // Call another artisan command
        $this->info('⚡ Running: php artisan otpfy:make LoginTemplate');
        Artisan::call('otpfy:make', ['name' => 'LoginTemplate']);
        $this->line(Artisan::output());

        $this->addEnvVariables();

        $this->info('✅ Otpfy installation complete!');
        $this->line("👉 Don't forget to include the routes file in routes/api.php:");
        $this->line("require base_path('routes/otpfy.php');");

        return self::SUCCESS;
    }

    /**
     * Publish a file from package to app.
     */
    protected function publishFile(string $source, string $destination, string $label): void
    {
        if (file_exists($destination)) {
            $this->warn("⚠️  {$label} already exists: {$destination}");
            if (! $this->confirm("Do you want to overwrite {$label}?")) {
                $this->info("⏩ Skipped: {$label}");
                return;
            }
        }

        if (! is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        if (copy($source, $destination)) {
            $this->info("✅ Published {$label}: {$destination}");
        } else {
            $this->error("❌ Failed to publish {$label}");
        }
    }




    protected function updateApiRoutesFile(): void
    {
        $apiFile = base_path('routes/api.php');
        $requireLine = "require base_path('routes/otpfy.php');";

        if (! file_exists($apiFile)) {
            $this->error("❌ routes/api.php not found.");
            return;
        }

        $content = file_get_contents($apiFile);

        // Check if it's already there
        if (str_contains($content, $requireLine)) {
            $this->info("⏩ routes/api.php already includes otpfy.php");
            return;
        }

        // Append at the end
        $content = rtrim($content) . PHP_EOL . PHP_EOL . $requireLine . PHP_EOL;

        if (file_put_contents($apiFile, $content) !== false) {
            $this->info("✅ Added require line to routes/api.php");
        } else {
            $this->error("❌ Failed to update routes/api.php");
        }
    }




    protected function addEnvVariables()
    {
        $vars = [
            'NETTYFISH_URL=',
            'NETTYFISH_API_KEY=',
            'NETTYFISH_SENDER_ID=',
            'NETTYFISH_SENDER_CHANNEL=',
            'NETTYFISH_SENDER_DCS=',
            'NETTYFISH_FLASH_SMS=',
            'NETTYFISH_SENDER_ROUTE=',
            'NETTYFISH_PEID=',
        ];

        foreach (['.env', '.env.example'] as $file) {
            $path = base_path($file);

            if (! file_exists($path)) {
                continue;
            }

            $content = file_get_contents($path);

            foreach ($vars as $var) {
                $key = explode('=', $var)[0];
                if (! str_contains($content, $key)) {
                    $content .= "\n{$var}";
                }
            }

            file_put_contents($path, $content);
        }

        $this->info('✅ Nettyfish env variables added to .env and .env.example');
    }
}
