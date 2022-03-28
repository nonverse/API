<?php

namespace App\Contracts\Repository;

use App\Models\InviteRequest;

interface InviteRequestRepositoryInterface
{
    /**
     * Get all invite requests
     *
     * @return mixed
     */
    public function index();

    /**
     * Get a request by email
     *
     * @param $email
     * @return mixed
     */
    public function get($email);

    /**
     * Create a new invite request
     *
     * @param array $data
     * @return InviteRequest
     */
    public function create(array $data): InviteRequest;

    /**
     * Update an invite request
     *
     * @param $email
     * @param array $data
     * @return mixed
     */
    public function update($email, array $data);

    /**
     * Delete an invite request
     *
     * @param $email
     * @return bool
     */
    public function delete($email): bool;

}
