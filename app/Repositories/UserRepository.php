<?php

namespace App\Repositories;

use App\Contracts\Repository\UserRepositoryInterface;
use Illuminate\Support\Str;
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
     * Get details for a specific user by UUID or Email
     *
     * @param $uuid
     *
     * @return object
     */
    public function get($uuid): object
    {
        $user = [];
        if (Str::isUuid($uuid)) {
            $user = User::query()->find($uuid);
        } else if (filter_var($uuid, FILTER_VALIDATE_EMAIL)) {
            $user = User::query()->where('email', $uuid)->first();
        }

        return $user;
    }

    /**
     * Create a user and persist to database
     *
     * @param array $data
     *
     * @return User
     */
    public function create(array $data): User
    {
        $user = new User;
        $user->uuid = $data['uuid'];
        $user->username = $data['username'];
        $user->name_first = $data['name_first'];
        $user->name_last = $data['name_last'];
        $user->email = $data['email'];
        $user->password = $data['password'];

        $query = $user->save();

        return $user;
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
