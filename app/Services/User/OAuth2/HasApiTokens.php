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
     * Check if a user's access token has a given scope
     *
     * @param $scope
     * @return bool
     */
    public function tokenCan($scope): bool
    {
        $parentScope = strtok($scope, '.') . '.*';

        if (in_array('*', explode(" ", $this->accessToken->scopes))) {
            return true;
        }
        if (in_array($parentScope, explode(" ", $this->accessToken->scopes))) {
            return true;
        }
        if (in_array($scope, explode(" ", $this->accessToken->scopes))) {
            return true;
        }
        return false;
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
