<?php

namespace App\Contracts\Repository;

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
     * @param bool $admin
     *
     * @return mixed
     */
    public function create(array $data, bool $admin);

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
