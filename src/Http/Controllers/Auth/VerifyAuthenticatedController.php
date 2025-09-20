<?php

namespace App\Http\Controllers\Auth;

use Krish033\Otpfy\Contracts\Auth;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class VerifyAuthenticatedController extends BaseController
{
    public function __invoke(Request $request, Auth $auth)
    {
        $request->validate([
            "mobile" => ['required', 'regex:/^[6-9]\d{9}$/'],
            "otp" => ['required', 'digits:6'],
        ]);

        if (!$auth->verifyOneTimePassword($request->mobile, $request->otp))
            throw new \Exception("Invalid OTP", 401);

        if (!($auth->findOne($request->mobile)))
            return $auth->register($request->mobile);

        return $auth->login($request->mobile);
    }
}
