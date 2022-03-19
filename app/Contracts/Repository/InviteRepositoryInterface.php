<?php

namespace App\Contracts\Repository;

use App\Models\Invite;

interface InviteRepositoryInterface
{
    /**
     * List all invites OR
     * List all invites created by a specific UUID
     *
     * @param $uuid
     * @return mixed
     */
    public function index($uuid = null);

    /**
     * Get a specific invite by email
     *
     * @param $email
     */
    public function get($email);

    /**
     * Create a new invite
     *
     * @param array $data
     * @return Invite
     */
    public function create(array $data): Invite;

    /**
     * Update a specific invite by email
     *
     * @param $email
     * @param array $data
     */
    public function update($email, array $data);

    /**
     * Delete a specific invite by email
     *
     * @param $email
     * @return bool
     */
    public function delete($email): bool;
}
