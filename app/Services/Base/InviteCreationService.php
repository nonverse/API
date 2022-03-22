<?php

namespace App\Services\Base;

use App\Contracts\Repository\InviteRepositoryInterface;
use App\Notifications\Invited;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Exception;

class InviteCreationService
{
    /**
     * @var InviteRepositoryInterface
     */
    private $repository;

    /**
     * @var Hasher
     */
    private $hasher;

    public function __construct(
        InviteRepositoryInterface $repository,
        Hasher                    $hasher
    )
    {
        $this->repository = $repository;
        $this->hasher = $hasher;
    }

    public function handle(Request $request, array $data)
    {
        $key = implode('-', str_split(strtoupper(Str::random(16)), 4));

        $invite = $this->repository->create([
            'email' => $data['email'],
            'name' => $data['name'],
            'invite_key' => $this->hasher->make($key),
            'invited_by' => $request->user()->uuid,
            'key_expiry' => CarbonImmutable::now()->addDays(30)
        ]);

        try {
            Notification::route('mail', $data['email'])
                ->notify(new Invited([
                    'key' => $key,
                    'email' => $data['email']
                ]));

            return $invite;
        } catch (Exception $e) {
            $this->repository->delete($data['email']);
            return false;
        }
    }
}
