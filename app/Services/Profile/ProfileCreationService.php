<?php

namespace App\Services\Profile;

use App\Contracts\Repository\AuthMeRepositoryInterface;
use App\Contracts\Repository\UserProfileRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\Profile;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;

class ProfileCreationService
{

    /**
     * @var UserProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var AuthMeRepositoryInterface
     */
    private $authMeRepository;

    public function __construct
    (
        UserProfileRepositoryInterface $profileRepository,
        UserRepositoryInterface        $userRepository,
        AuthMeRepositoryInterface      $authMeRepository
    )
    {
        $this->profileRepository = $profileRepository;
        $this->userRepository = $userRepository;
        $this->authMeRepository = $authMeRepository;
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
        $user = $this->userRepository->get($uuid);
        $profile = $this->profileRepository->create($uuid, array(
            'uuid' => $uuid,
            'mc_uuid' => $response['id'],
            'mc_username' => $response['name'],
            'profile_verified_at' => CarbonImmutable::now()
        ));
        $authme = $this->authMeRepository->create($uuid, [
            'mc_username' => $response['name'],
            'password_hash' => User::query()->find($uuid)->password
        ]);

        if ($profile && $authme) {
            // Send confirmation of linking to user
            Http::withToken(env('PANEL_ADMIN_KEY'))->post('https://' . env('PANEL_APP') . '/api/client/servers/' . env('MINECRAFT_LOBBY_SERVER') . '/command', [
                'command' => 'tellraw ' . $response['name'] . ' ["",{"text":"Your profile has successfully been linked to a Nonverse account with email ","bold":true,"color":"green"},{"text":"' . $user->email . '","color":"gold"}]'
            ]);
        }

        return $profile;
    }
}
