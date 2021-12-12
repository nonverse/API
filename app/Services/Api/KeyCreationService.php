<?php

namespace App\Services\Api;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Notifications\ApiKeyCreated;
use Exception;

class KeyCreationService
{

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * @param $uuid
     * @param array $data
     * @return bool
     */
    public function handle($uuid, array $data): bool
    {
        // Fetch the user by UUID
        $user = $this->repository->get($uuid);

        // Create a token for the user
        $token = $user->createToken($data['key_name'], $data['permissions']);

        try {
            // Attempt to send an email notification to the user with API Key details
            $user->notify(new ApiKeyCreated($user, array(
                'key_name' => $data['key_name'],
                'permission_count' => count($data['permissions']),
                'token_id' => explode('|', $token->plainTextToken)[0],
                'token_value' => explode('|', $token->plainTextToken)[1]
            )));

            return true;
        } catch (Exception $e) {
            // TODO Delete API Key from database if unable to send email
            return false;
        }
    }
}
