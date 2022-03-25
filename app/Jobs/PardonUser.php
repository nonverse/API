<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\UserPardoned;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PardonUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        User $user
    )
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::query()->findOrFail($this->user->uuid);
        $user->violation_ends_at = null;
        $user->violations = null;
        $pardon = $user->save();

        if ($pardon) {
            $this->user->notify(new UserPardoned($this->user));
        }
    }
}
