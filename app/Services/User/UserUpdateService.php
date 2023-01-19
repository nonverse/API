<?php

namespace App\Services\User;

use App\Contracts\Repository\UserRepositoryInterface;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class UserUpdateService
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var Hasher
     */
    private Hasher $hasher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        Hasher                  $hasher
    )
    {
        $this->repository = $userRepository;
        $this->hasher = $hasher;
    }

    /**
     * @param $uuid
     * @param array $data
     * @return Exception|Model
     * @throws Exception
     */
    public function handle($uuid, array $data): Model|Exception
    {
        $user = $this->repository->get($uuid);

        /**
         * Create new password hash if required
         */
        if (Arr::has($data, 'password')) {
            $data['password'] = $this->hasher->make($data['password']);
        }

        /**
         * Format date of birth to Carbon time
         */
        if (Arr::has($data, 'dob')) {
            $data['dob'] = CarbonImmutable::parse($data['dob'])->format('Y-m-d');
        }

        /**
         * Resend email verification if required
         */
        if (Arr::has($data, 'email') && $data['email'] !== $user['email']) {
            /*
             * This will update the user's email in memory for the purpose of sending the
             * verification email. No data has been persisted to the database yet
             */
            $user->fill([
                'email' => $data['email']
            ]);

            try {
                /**
                 * If email verification is successfully sent un-verify previous email
                 */
                $user->sendEmailVerificationNotification();

                $data = [
                    ...$data,
                    'email_verified_at' => null
                ];
            }
            catch (Exception) {
                /**
                 * Don't update user's email if unable to send verification
                 */
                Arr::forget($data, 'email');
            }
        }

        /**
         * Update the user's data and persist to database
         */
        try {
            $user = $this->repository->update($uuid, $data);
        } catch (Exception $e) {
            return $e;
        }

        return $user;
    }
}
