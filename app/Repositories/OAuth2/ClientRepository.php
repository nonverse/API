<?php

namespace App\Repositories\OAuth2;

use App\Models\OAuth2\Client;

class ClientRepository extends \App\Repositories\Repository implements \App\Contracts\Repository\OAuth2\ClientRepositoryInterface
{

    public function model(): string
    {
        return Client::class;
    }
}
