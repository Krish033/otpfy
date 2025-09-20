<?php

declare(strict_types=1);

namespace Krish033\Otpfy\Contracts;

use App\Models\User;

interface Auth
{




    /**
     * Find a single user by mobile number.
     *
     * @param string $mobile
     * @return User
     */
    public function findOne(string $mobile): User|bool;








    /**
     * Generate a one-time password (OTP) for the given mobile number.
     *
     * @param string $mobile
     * @param string|null $name Optional name to cache for registration
     * @param string|null $role Optional role to cache for registration
     * @return int
     */
    public function generateOneTimePassword(string $mobile, ?string $name = null, ?string $role = null): int;







    /**
     * Send a one-time password (OTP) to the given mobile number.
     *
     * @param string $mobile
     * @return void
     */
    public function sendOneTimePasswordToUser(string $mobile): void;







    /**
     * Verify the one-time password for the given mobile number.
     *
     * @param string $mobile
     * @param string $otp
     * @return bool
     */
    public function verifyOneTimePassword(string $mobile, string $otp): bool;








    /**
     * Login user by mobile number and return JWT token.
     *
     * @param string $mobile
     * @return string
     */
    public function login(string $mobile): string;








    /**
     * Register a new user with mobile number and return JWT token.
     *
     * @param string $mobile
     * @return string
     */
    public function register(string $mobile): string;
}
