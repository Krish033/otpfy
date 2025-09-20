<?php

declare(strict_types=1);

namespace Krish033\Otpfy\Repositories;

use Krish033\Otpfy\Contracts\Auth;
use App\Models\User;
use App\Otpfy\Templates\LoginTemplate;
use Exception;
use Illuminate\Support\Facades\Cache;
use Krish033\Otpfy\Facades\Message;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepository implements Auth
{







    /**
     * Find a single user by mobile number.
     *
     * @param string $mobile
     * @return User
     * @throws Exception If user is not found.
     */
    public function findOne(string $mobile): User|bool
    {
        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
            return false;
        }

        return $user;
    }








    /**
     * Generate a 6-digit OTP and store it in cache.
     *
     * @param string $mobile
     * @param string|null $name Optional name to cache for registration
     * @param string|null $role Optional role to cache for registration
     * @return int Generated OTP
     */
    public function generateOneTimePassword(string $mobile, ?string $name = null, ?string $role = null): int
    {
        $otp = random_int(100000, 999999); // cryptographically safer than rand()

        if ($name) {
            Cache::put("name_$mobile", $name, now()->addMinutes(1));
        }

        if ($role) {
            Cache::put("role_$mobile", $role, now()->addMinutes(1));
        }

        // Store the OTP with short expiration
        Cache::put("otp_$mobile", $otp, now()->addMinutes(1));

        return $otp;
    }







    /**
     * Send the generated OTP to a user's mobile number.
     *
     * @param string $mobile
     * @return void
     */
    public function sendOneTimePasswordToUser(string $mobile): void
    {
        $otp = $this->generateOneTimePassword($mobile);

        // Send OTP using Nettyfish via custom template
        Message::send(new LoginTemplate(otp: strval($otp)))->to($mobile);
    }






    /**
     * Verify a user's submitted OTP against cached OTP.
     *
     * @param string $mobile
     * @param string $otp
     * @return bool
     * @throws Exception If OTP is invalid or expired.
     */
    public function verifyOneTimePassword(string $mobile, string $otp): bool
    {
        $cachedOtp = Cache::get("otp_$mobile");

        if (!$cachedOtp || $cachedOtp != $otp) {
            throw new Exception('Invalid or expired OTP.');
        }

        // Clear OTP after successful verification
        $this->flashOneTimePassword($mobile);

        return true;
    }







    /**
     * Login user by mobile number and return JWT token.
     *
     * @param string $mobile
     * @return string JWT token
     * @throws Exception If user is not found.
     */
    public function login(string $mobile): string
    {
        $user = $this->findOne($mobile);

        return JWTAuth::fromUser($user);
    }






    /**
     * Register a new user with mobile number, then return JWT token.
     * Name and role can be pre-cached from OTP flow.
     *
     * @param string $mobile
     * @return string JWT token
     */
    public function register(string $mobile): string
    {
        $user = User::create([
            'mobile' => $mobile,
            'name'   => Cache::get("name_$mobile", 'New User'),
            'role'   => Cache::get("role_$mobile", 2),
        ]);

        return JWTAuth::fromUser($user);
    }






    /**
     * Remove cached OTP, name, and role for a mobile number.
     *
     * @param string $mobile
     * @return void
     */
    protected function flashOneTimePassword(string $mobile): void
    {
        Cache::forget("otp_$mobile");
        Cache::forget("name_$mobile");
        Cache::forget("role_$mobile");
    }
}
