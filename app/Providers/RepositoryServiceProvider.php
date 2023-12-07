<?php

namespace App\Providers;

use App\Contracts\Repository\Auth\AuthorizationTokenRepositoryInterface;
use App\Contracts\Repository\Auth\RecoveryRepositoryInterface;
use App\Contracts\Repository\OAuth2\AccessTokenRepositoryInterface;
use App\Contracts\Repository\RepositoryInterface;
use App\Contracts\Repository\SettingsRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Repositories\Auth\AuthorizationTokenRepository;
use App\Repositories\Auth\RecoveryRepository;
use App\Repositories\OAuth2\AccessTokenRepository;
use App\Repositories\Repository;
use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, Repository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RecoveryRepositoryInterface::class, RecoveryRepository::class);
        $this->app->bind(AuthorizationTokenRepositoryInterface::class, AuthorizationTokenRepository::class);
        $this->app->bind(AccessTokenRepositoryInterface::class, AccessTokenRepository::class);
        $this->app->bind(SettingsRepositoryInterface::class, SettingsRepository::class);
    }
}
