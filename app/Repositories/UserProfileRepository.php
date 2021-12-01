<?php

namespace App\Repositories;

use App\Contracts\Repository\UserProfileRepositoryInterface;
use App\Models\Profile;

class UserProfileRepository implements UserProfileRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function index()
    {
        // TODO: Implement index() method.
    }

    /**
     * @inheritDoc
     */
    public function get($uuid)
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritDoc
     */
    public function create($uuid, array $data): Profile
    {
        $profile = new Profile;
        $profile->uuid = $uuid;
        $profile->mc_uuid = $data['mc_uuid'];
        $profile->mc_username = $data['mc_username'];
        $profile->profile_verified_at = $data['profile_verified_at'];

        $query = $profile->save();

        return $profile;
    }

    /**
     * @inheritDoc
     */
    public function update($uuid, array $data)
    {
        // TODO: Implement update() method.
    }

    /**
     * @inheritDoc
     */
    public function delete($uuid): bool
    {
        // TODO: Implement delete() method.
    }
}
