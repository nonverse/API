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

    public function construct(
        InviteRepositoryInterface $repository,
        Hasher                    $hasher
    )
    {
        $this->repository = $repository;
        $this->hasher = $hasher;
    }

    /**
     * Issue activation token
     *
     * @param Request $request
     * @param $email
     * @param $key
     * @return array
     */
    public function handle(Request $request, $email, $key): array
    {
        $invite = $this->repository->get($email);

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

        $token = Str::random(64);

        $request->session()->put('activation_token', [
            'email' => $request->input('email'),
            'token_value' => $token,
            'token_expiry' => CarbonImmutable::now()->addMinutes(15)
        ]);

        return [
            'success' => true,
            'token' => $token
        ];
    }
}
