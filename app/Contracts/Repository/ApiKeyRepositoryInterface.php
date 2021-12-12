<?php

namespace App\Contracts\Repository;

use App\Models\User;

interface ApiKeyRepositoryInterface
{

    /**
     * Get simple details of all API Keys belonging to a user
     *
     * @param User $user
     * @return object
     */
    public function get(User $user): object;
}
