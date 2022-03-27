<?php

namespace App\Contracts\Repository;

use App\Models\AuthMe;

interface AuthMeRepositoryInterface
{
    /**
     * Create an AuthMe for a user
     *
     * @param $uuid
     * @param array $data
     * @return AuthMe
     */
    public function create($uuid, array $data): AuthMe;

    /**
     * Get a specific user's AuthMe
     *
     * @param $uuid
     * @return mixed
     */
    public function get($uuid);

    /**
     * Update a user's AuthMe
     *
     * @param $uuid
     * @param array $data
     * @return AuthMe|bool
     */
    public function update($uuid, array $data);

    /**
     * Delete a user's AuthMe
     *
     * @param $uuid
     * @return bool
     */
    public function delete($uuid): bool;
}
