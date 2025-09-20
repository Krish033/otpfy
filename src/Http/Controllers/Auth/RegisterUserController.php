<?php

namespace App\Http\Controllers\Auth;

use Krish033\Otpfy\Contracts\Auth;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class RegisterUserController extends BaseController
{
    public function __invoke(Request $request, Auth $auth)
    {

        $request->validate([
            "mobile" => ['required', 'regex:/^[6-9]\d{9}$/'],
            "name" => ['required', 'string'],
            "role" => 'required|in:1,2',
        ]);


        if (($auth->findOne($request->mobile)))
            throw new \Exception('Conflict, You already have an account.');

        return $auth->generateOneTimePassword($request->mobile, $request->name, $request->role);
    }
}
