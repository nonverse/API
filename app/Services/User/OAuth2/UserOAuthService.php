<?php

namespace App\Services\User\OAuth2;

use App\Contracts\Repository\OAuth2\AccessTokenRepositoryInterface;
use App\Models\User;
use App\Repositories\OAuth2\ClientRepository;
use Exception;

class UserOAuthService
{
    /**
     * @var AccessTokenRepositoryInterface
     */
    private AccessTokenRepositoryInterface $accessTokenRepository;

    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    public function __construct(
        AccessTokenRepositoryInterface $accessTokenRepository,
        ClientRepository               $clientRepository
    )
    {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * Get all clients that a user has authorized on their account
     *
     * @param User $user
     * @return array
     * @throws Exception
     */
    public function authorizedClients(User $user): array
    {
        $tokens = $this->accessTokenRepository->getBuilder()->where('user_id', $user->uuid)->get();

        $clients = [];
        foreach ($tokens as $token) {
            if (!in_array($token->client_id, $clients)) {
                $client = $this->clientRepository->get($token->client_id);
                $clients[$client->id] = [
                    'name' => $client->name,
                    'url' => $client->redirect,
                    'third_party' => (bool)$client->user_id,
                    'connection' => 'oauth'
                ];
            }
        }

        return $clients;
    }
}
