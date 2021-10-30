<?php

namespace App\Repositories;

use App\Contracts\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{

    /**
     * Get a list of all registered users
     *
     * @return mixed
     */
    public function index()
    {
        // TODO: Implement index() method.
    }

    /**
     * Get details for a specific user by UUID
     *
     * @param $uuid
     *
     * @return object
     */
    public function get($uuid): object
    {
        // TODO: Implement get() method.
    }

    /**
     * Create a user and persist to database
     *
     * @param array $data
     *
     * @return Response
     */
    public function create(array $data): Response
    {
        $user = new User;
        $user->uuid = $data['uuid'];
        $user->username = $data['username'];
        $user->name_first = $data['name_first'];
        $user->name_last = $data['name_last'];
        $user->email = $data['email'];
        $user->password = $data['passwordhash'];

        $query = $user->save();

        if ($query) {
            $response = response($data['uuid'], 200);
        } else {
            $response = response('Unable to query database', 500);
        }

        return $response;
    }

    /**
     * Update a user by UUID
     *
     * @param $uuid
     * @param array $data
     *
     * @return bool
     */
    public function update($uuid, array $data): bool
    {
        // TODO: Implement update() method.
    }

    /**
     * Delete a registered user from database
     *
     * @param $uuid
     * @return bool
     */
    public function delete($uuid): bool
    {
        // TODO: Implement delete() method.
    }
}
