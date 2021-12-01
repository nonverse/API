<?php

namespace App\Contracts\Repository;

use App\Models\Profile;

interface UserProfileRepositoryInterface
{

    /**
     * Get a list of all user profiles
     *
     * @return mixed
     */
    public function index();

    /**
     * Get a specific user's profile by UUID
     *
     * @param $uuid
     *
     * @return mixed
     */
    public function get($uuid);

    /**
     * Create a user profile and persist to database
     *
     * @param $uuid
     * @param array $data
     *
     * @return Profile
     */
    public function create($uuid, array $data): Profile;

    /**
     * Update a user profile by UUID
     *
     * @param $uuid
     * @param array $data
     *
     * @return Profile|bool
     */
    public function update($uuid, array $data);

    /**
     * Delete a user's profile
     *
     * @param $uuid
     * @return bool
     */
    public function delete($uuid): bool;
}
