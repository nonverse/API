<?php

namespace App\Services\Auth;

use App\Contracts\Repository\Auth\OneTimePasswordRepositoryInterface;
use App\Models\User;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OneTimePasswordService
{
    /**
     * @var OneTimePasswordRepositoryInterface
     */
    private OneTimePasswordRepositoryInterface $repository;

    /**
     * @var Hasher
     */
    private Hasher $hasher;

    public function __construct(
        OneTimePasswordRepositoryInterface $oneTimePasswordRepository,
        Hasher                             $hasher
    )
    {
        $this->repository = $oneTimePasswordRepository;
        $this->hasher = $hasher;
    }

    /**
     * Send verification code to specified channel
     *
     * @param User $user
     * @param string $channel
     * @param string $actionId
     * @return array|false[]|void
     */
    public function send(User $user, string $channel, string $actionId)
    {
        /**
         * ID that is assigned to the OTP
         * This ID should be sent in the verification request and acts as a username for the OTP
         **/
        $id = Str::random(100);
        $code = Str::random(config('auth.one_time_passwords.length'));

        try {
            /**
             * Create new OTP entry
             */
            $this->repository->create([
                'id' => $id,
                'user_id' => $user->uuid,
                'action_id' => $actionId,
                'value' => $this->hasher->make($code),
                'channel' => $channel,
                'expires_at' => CarbonImmutable::now()->addMinutes(config('auth.one_time_passwords.expiry'))
            ], true);
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        if ($channel == 'email') {
            return [
                'success' => false
            ];
        }
    }
}
