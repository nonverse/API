<?php

namespace App\Console\Commands;

use App\Contracts\Repository\InviteRepositoryInterface;
use App\Notifications\Invited;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class InviteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nonverse:invite {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invite a user to Nonverse';

    /**
     * @var InviteRepositoryInterface
     */
    private $repository;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        InviteRepositoryInterface $repository,
        Hasher                    $hasher
    )
    {
        $this->repository = $repository;
        $this->hasher = $hasher;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $key = implode('-', str_split(strtoupper(Str::random(16)), 4));

        $invite = $this->repository->create([
            'email' => $email,
            'invite_key' => $this->hasher->make($key),
            'name' => 'Isuru Abhayaratne',
            'invited_by' => 'nonverse-arti-san0-cli0-' . Str::random(12),
            'key_expiry' => CarbonImmutable::now()->addDays(30)
        ]);

        Notification::route('mail', $email)
            ->notify(new Invited([
                'key' => $key,
                'email' => $email
            ]));

        return Command::SUCCESS;
    }
}
