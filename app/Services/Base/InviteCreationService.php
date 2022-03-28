<?php

namespace App\Services\Base;

use App\Contracts\Repository\InviteRepositoryInterface;
use App\Contracts\Repository\InviteRequestRepositoryInterface;
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
     * @var InviteRequestRepositoryInterface
     */
    private $requestRepository;

    /**
     * @var Hasher
     */
    private $hasher;

    public function __construct(
        InviteRepositoryInterface        $repository,
        InviteRequestRepositoryInterface $requestRepository,
        Hasher                           $hasher
    )
    {
        $this->repository = $repository;
        $this->requestRepository = $requestRepository;
        $this->hasher = $hasher;
    }

    public function handle(Request $request, $email)
    {
        $key = implode('-', str_split(strtoupper(Str::random(16)), 4));

        $inviteRequest = $this->requestRepository->get($email);

        $invite = $this->repository->create([
            'email' => $inviteRequest->email,
            'name' => $inviteRequest->name,
            'invite_key' => $this->hasher->make($key),
            'invited_by' => $request->user()->uuid,
            'key_expiry' => CarbonImmutable::now()->addDays(30)
        ]);

        try {
            Notification::route('mail', $email)
                ->notify(new Invited([
                    'key' => $key,
                    'email' => $email
                ]));
        } catch (Exception $e) {
            $this->repository->delete($email);
            return false;
        }
        $this->requestRepository->update($email, [
            'accepted_by' => $request->user()->uuid
        ]);
        return $invite;
    }
}
