<?php

namespace App\Services\User\OAuth2;

use App\Models\OAuth2\AccessToken;

trait HasApiTokens
{
    /**
     * @var AccessToken
     */
    protected AccessToken $accessToken;

    /**
     * Get the access token used by the user
     *
     * @return AccessToken
     */
    public function token(): AccessToken
    {
        return $this->accessToken;
    }

    /**
     * Add access token to user instance
     *
     * @param $accessToken
     * @return $this
     */
    public function withAccessToken($accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }


}
