<?php

namespace App\Repositories;

use App\Contracts\Repository\ApiKeyRepositoryInterface;
use App\Models\PersonalAccessToken;
use App\Models\User;

class ApiKeyRepository implements ApiKeyRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function get(User $user): object
    {
        return $user->tokens()->get()->map->only(['id', 'name', 'last_used_at']);
    }
}
