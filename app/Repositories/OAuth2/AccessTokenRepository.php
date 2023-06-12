<?php

namespace App\Repositories\OAuth2;

use App\Models\OAuth2\AccessToken;

class AccessTokenRepository extends \App\Repositories\Repository implements \App\Contracts\Repository\OAuth2\AccessTokenRepositoryInterface
{

    public function model(): string
    {
        return AccessToken::class;
    }
}
