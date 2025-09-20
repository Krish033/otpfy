<?php

namespace App\Http\Controllers\Auth;

use Krish033\Otpfy\Contracts\Auth;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends BaseController
{
    public function __invoke(Request $request, Auth $auth)
    {
        $request->validate([
            "mobile" => ['required', 'regex:/^[6-9]\d{9}$/'],
        ]);

        if (!($auth->findOne($request->mobile)))
            throw new \Exception('User not found.');

        return $auth->generateOneTimePassword($request->mobile);
    }
}
