<?php

namespace App\Services\Base;

use App\Contracts\Repository\InviteRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Services\Users\UserCreationService;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

class InviteActivationService
{
    /**
     * @var InviteRepositoryInterface
     */
    private $repository;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var UserCreationService
     */
    private $userCreationService;


    public function construct(
        InviteRepositoryInterface $repository,
        UserCreationService       $userCreationService,
        Hasher                    $hasher
    )
    {
        $this->repository = $repository;
        $this->userCreationService = $userCreationService;
        $this->hasher = $hasher;
    }

    public function handle($email, $key)
    {
        $invite = $this->repository->get($email);
        $uuid = Str::uuid();

        if (CarbonImmutable::now()->isAfter($invite->key_expiry)) {
            return [
                'success' => false,
                'error' => 'Activation key has expired'
            ];
        }

        if (!$this->hasher->check($key, $invite->invite_key)) {
            return [
                'success' => false,
                'error' => 'Invalid activation key'
            ];
        }

        try {
            $this->userCreationService->handle([
                'email' => $email,
                'uuid' => $uuid
            ]);

            $this->repository->update($email, [
                'claimed_by' => $uuid
            ]);

            return [
                'success' => true,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Something went wrong'
            ];
        }
    }
}
