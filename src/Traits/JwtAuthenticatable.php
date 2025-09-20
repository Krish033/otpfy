<?php

namespace Krish033\Otpfy\Traits;

use Tymon\JWTAuth\Contracts\JWTSubject;

trait JwtAuthenticatable
{
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
