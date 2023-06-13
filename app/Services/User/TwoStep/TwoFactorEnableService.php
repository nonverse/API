<?php

namespace App\Services\User\TwoStep;

use App\Contracts\Repository\RecoveryRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;
use App\Notifications\TwoFactorEnabled;
use Exception;
use http\Exception\RuntimeException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorEnableService
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var RecoveryRepositoryInterface
     */
    private RecoveryRepositoryInterface $recoveryRepositroy;

    /**
     * @var Encrypter
     */
    private Encrypter $encrypter;

    /**
     * @var Google2FA
     */
    private Google2FA $google2FA;

    public function __construct(
        UserRepositoryInterface     $repository,
        RecoveryRepositoryInterface $recoveryRepository,
        Encrypter                   $encrypter,
        Google2FA                   $google2FA
    )
    {
        $this->repository = $repository;
        $this->recoveryRepositroy = $recoveryRepository;
        $this->encrypter = $encrypter;
        $this->google2FA = $google2FA;
    }

    /**
     * Enable 2FA on a user's account
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     * @throws InvalidCharactersException
     */
    public function handle(User $user, $code): array
    {
        try {
            $secret = $this->encrypter->decrypt($user->totp_secret);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (!$this->google2FA->verifyKey($secret, $code)) {
            return [
                'success' => false
            ];
        }

        $token = Str::random(24);

        $this->repository->update($user->uuid, [
            'use_totp' => 1,
//            'totp_recovery_token' => Hash::make($token),
//            'totp_authenticated_at' => CarbonImmutable::now()
        ]);

        $this->recoveryRepositroy->update($user->uuid, [
            'totp_token' => Hash::make($token)
        ]);

        try {
            $user->notify(new TwoFactorEnabled($user, $token));
        } catch (Exception $e) {
            $this->repository->update($user->uuid, [
                'use_totp' => 0,
//                'totp_recovery_token' => null,
//                'totp_authenticated_at' => CarbonImmutable::now()
            ]);

            $this->recoveryRepositroy->update($user->uuid, [
                'totp_token' => null
            ]);

            return [
                'success' => false
            ];
        }

        return [
            'success' => true,
            'uuid' => $user->uuid
        ];
    }
}
