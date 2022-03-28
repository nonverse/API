<?php

namespace App\Providers;

use App\Contracts\Repository\ApiKeyRepositoryInterface;
use App\Contracts\Repository\AuthMeRepositoryInterface;
use App\Contracts\Repository\InviteRepositoryInterface;
use App\Contracts\Repository\InviteRequestRepositoryInterface;
use App\Contracts\Repository\UserProfileRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Repositories\ApiKeyRepository;
use App\Repositories\AuthMeRepository;
use App\Repositories\InviteRepository;
use App\Repositories\InviteRequestRepository;
use App\Repositories\UserProfileRepository;
use App\Repositories\UserRepository;
use Carbon\Laravel\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserProfileRepositoryInterface::class, UserProfileRepository::class);
        $this->app->bind(ApiKeyRepositoryInterface::class, ApiKeyRepository::class);
        $this->app->bind(InviteRepositoryInterface::class, InviteRepository::class);
        $this->app->bind(AuthMeRepositoryInterface::class, AuthMeRepository::class);
        $this->app->bind(InviteRequestRepositoryInterface::class, InviteRequestRepository::class);
    }

}
