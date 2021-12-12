<?php

namespace App\Providers;

use App\Contracts\Repository\ApiKeyRepositoryInterface;
use App\Contracts\Repository\UserProfileRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Repositories\ApiKeyRepository;
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
    }

}
