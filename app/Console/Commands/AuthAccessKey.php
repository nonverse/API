<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

class AuthAccessKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nonverse:authkey';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate API Access key for SecureAuth';

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
        Hasher $hasher
    )
    {
        $this->hasher = $hasher;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $key = Str::random(64);
        $keyEnv = 'AUTH_KEY_HASH=' . $this->hasher->make($key);

        file_put_contents('.env', str_replace('AUTH_KEY_HASH=', $keyEnv, file_get_contents('.env')));
        $this->line('Auth Key: ' . $key);
        return Command::SUCCESS;
    }
}
