<?php

namespace App\Services\Profile;

use App\Contracts\Repository\UserProfileRepositoryInterface;
use App\Models\Profile;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;

class ProfileCreationService
{

    /**
     * @var UserProfileRepositoryInterface
     */
    private $repository;

    public function __construct
    (
        UserProfileRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Handle profile creation
     *
     * @param $uuid
     * @param $mc_username
     * @return Profile
     */
    public function handle($uuid, $mc_username): Profile
    {
        $response = Http::get('https://api.mojang.com/users/profiles/minecraft/' . $mc_username);
        return $this->repository->create($uuid, array(
            'uuid' => $uuid,
            'mc_uuid' => $response['id'],
            'mc_username' => $response['name'],
            'profile_verified_at' => CarbonImmutable::now()
        ));
    }
}