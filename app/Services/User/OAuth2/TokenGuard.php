<?php

namespace App\Services\User\OAuth2;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;

class TokenGuard implements \Illuminate\Contracts\Auth\Guard
{
    use GuardHelpers, Macroable;

    /**
     * @var Request
     */
    private Request $request;

    public function __construct(
        Request $request
    )
    {
        $this->request = $request;
    }

    public function user()
    {
        // TODO: Implement user() method.
    }

    public function validate(array $credentials = [])
    {
        // TODO: Implement validate() method.
    }

    protected function authenticateViaBearerToken()
    {
        if ($this->request->bearerToken()) {
            $accessToken = JWT::decode($this->request->bearerToken(), new Key(config('oauth.public_key'), 'RS256'));
        }
    }
}
