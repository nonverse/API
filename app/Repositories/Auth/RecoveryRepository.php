<?php

namespace App\Repositories\Auth;

use App\Models\Recovery;
use App\Repositories\Repository;

class RecoveryRepository extends Repository implements \App\Contracts\Repository\Auth\RecoveryRepositoryInterface
{

    public function model(): string
    {
        return Recovery::class;
    }
}
