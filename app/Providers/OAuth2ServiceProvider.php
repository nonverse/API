<?php

namespace App\Providers;

use App\Repositories\OAuth2\AccessTokenRepository;
use App\Repositories\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class OAuth2ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::viaRequest('oauth2', function(Request $request) {
            if ($request->bearerToken()) {
                try {
                    $jwt = (array)JWT::decode($request->bearerToken(), new Key(config('oauth.public_key'), 'RS256'));
                } catch (Exception $e) {
                    return;
                }
                $accessToken = (new AccessTokenRepository($this->app))->get($jwt['jti']);

                //TODO validate client

                if (!$accessToken->revoked) {
                    return (new UserRepository($this->app))->get($accessToken->user_id)->withAccessToken($accessToken);
                }
            }
        });
    }
}
