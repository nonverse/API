<?php

namespace App\Contracts\Repository;

use App\Models\User;
use Illuminate\Support\Facades\App;

interface UserRepositoryInterface {

    /**
     * Get a list of all registered users
     *
     * @return mixed
     */
    public function index();

    /**
     * Get details for a specific user by UUID
     *
     * @param $uuid
     *
     * @return object
     */
    public function get($uuid): object;

    /**
     * Create a user and persist to database
     *
     * @param array $data
     *
     * @return User
     */
    public function create(array $data);

    /**
     * Update a user by UUID
     *
     * @param $uuid
     * @param array $data
     *
     * @return bool
     */
    public function update($uuid, array $data): bool;

    /**
     * Delete a registered user from database
     *
     * @param $uuid
     * @return bool
     */
    public function delete($uuid): bool;
}
