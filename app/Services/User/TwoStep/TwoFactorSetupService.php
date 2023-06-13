<?php

namespace App\Services\User\TwoStep;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;
use Exception;
use http\Exception\RuntimeException;
use Illuminate\Contracts\Encryption\Encrypter;
use JetBrains\PhpStorm\ArrayShape;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorSetupService
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var Encrypter
     */
    private Encrypter $encrypter;

    /**
     * @var Google2FA
     */
    private Google2FA $google2FA;

    public function __construct(
        UserRepositoryInterface $repository,
        Encrypter               $encrypter,
        Google2FA               $google2FA
    )
    {
        $this->repository = $repository;
        $this->encrypter = $encrypter;
        $this->google2FA = $google2FA;
    }

    /**
     * Create a new 2FA secret for a user and
     * Generate QRCode URL
     *
     * @param User $user
     * @return array
     * @throws Exception
     */
    #[ArrayShape(['qrcode_data' => "string", 'secret' => "string"])] public function handle(User $user): array
    {
        $secret = '';
        try {
            $secret = $this->google2FA->generateSecretKey();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        $this->repository->update($user->uuid, [
            'totp_secret' => $this->encrypter->encrypt($secret)
        ]);

        return [
            'qrcode_data' => $this->google2FA->getQRCodeUrl(
                'Nonverse Studios',
                $user->email,
                $secret
            ),
            'secret' => $secret
        ];
    }
}
