<?php

namespace App\Services\Api;

use App\Contracts\Repository\UserRepositoryInterface;

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
     * @return array
     */
    public function handle($uuid, array $data): array
    {
        $user = $this->repository->get($uuid);
        $token = $user->createToken($data['key_name'], $data['permissions']);

        return array(
            'key_name' => $data['key_name'],
            'permission_count' => count($data['permissions']),
            'token_id' => explode('|', $token->plainTextToken)[0],
            'token_value' => explode('|', $token->plainTextToken)[1]
        );
    }
}
