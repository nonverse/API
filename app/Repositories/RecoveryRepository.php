<?php

namespace App\Repositories;

use App\Models\Recovery;

class RecoveryRepository extends Repository implements \App\Contracts\Repository\RecoveryRepositoryInterface
{

    public function model(): string
    {
        return Recovery::class;
    }
}
