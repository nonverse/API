<?php

namespace App\Repositories;

use App\Contracts\Repository\UserProfileRepositoryInterface;
use App\Models\Profile;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

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
        $profile = [];
        if (Str::isUuid($uuid)) {
            $profile = Profile::query()->find($uuid);
        }

        return $profile;
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

        return $this->get($uuid);
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
        try {
            $profile = Profile::query()->find($uuid)->firstOrFail();
            $profile->delete();
        } catch (QueryException $e) {
            return false;
        }


        return true;
    }
}
