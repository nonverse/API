<?php

namespace App\Repositories\Auth;

use App\Models\OneTimePassword;

class OneTimePasswordRepository extends \App\Repositories\Repository implements \App\Contracts\Repository\Auth\OneTimePasswordRepositoryInterface
{

    public function model(): string
    {
        return OneTimePassword::class;
    }
}
