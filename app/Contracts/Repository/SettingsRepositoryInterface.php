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

    /**
     * @param string $uuid
     * @param string $key
     * @param string $value
     * @return Model
     */
    public function updateByUuidAndKey(string $uuid, string $key, string $value): Model;
}
