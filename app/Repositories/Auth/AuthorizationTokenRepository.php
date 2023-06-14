<?php

namespace App\Repositories\Auth;

use App\Models\Auth\AuthorizationToken;

class AuthorizationTokenRepository extends \App\Repositories\Repository implements \App\Contracts\Repository\Auth\AuthorizationTokenRepositoryInterface
{

    public function model(): string
    {
        return AuthorizationToken::class;
    }
}
