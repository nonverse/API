<?php

namespace App\Services\User;

use App\Contracts\Repository\Auth\RecoveryRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use Exception;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserCreationService
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var RecoveryRepositoryInterface
     */
    private RecoveryRepositoryInterface $recoveryRepository;

    /**
     * @var Hasher
     */
    private Hasher $hasher;

    public function __construct(
        UserRepositoryInterface     $userRepository,
        RecoveryRepositoryInterface $recoveryRepository,
        Hasher                      $hasher,
    )
    {
        $this->repository = $userRepository;
        $this->recoveryRepository = $recoveryRepository;
        $this->hasher = $hasher;
    }

    /**
     * Create a new user and persist to database
     *
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function handle(array $data): Model
    {

        /**
         * Generate a new UUID and password hash
         */
        $data = [
            ...$data,
            'uuid' => Str::uuid(),
            'password' => $this->hasher->make($data['password'])
        ];

        try {
            /**
             * Create new user in database
             */
            $user = $this->repository->create($data, true);
            /**
             * Create new user recovery entry
             */
            $this->recoveryRepository->create([
                'uuid' => $user->uuid,
            ], true);
            /**
             * Send email verification
             */
            $user->sendEmailVerification();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $user;
    }
}
