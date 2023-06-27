<?php

namespace App\Contracts\Repository;

use Illuminate\Database\Eloquent\Model;

interface SettingsRepositoryInterface extends RepositoryInterface
{
    /**
     * Get a user's settings
     *
     * @param string $uuid
     * @return object
     */
    public function getUserSettings(string $uuid): object;
}
