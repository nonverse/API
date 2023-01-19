<?php

namespace App\Providers;

use App\Contracts\Repository\RepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Repositories\Repository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, Repository::class);
    }
}
