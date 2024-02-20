<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use phpseclib3\Crypt\RSA;

class Keys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Api encryption keys';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $publicKey = './storage/api-public.key';
        $privateKey = './storage/api-private.key';

        $xsPublicKey = './storage/api:xs-public.key';
        $xsPrivateKey = './storage/api:xs-private.key';


        $key = RSA::createKey(4096);
        $xsKey = RSA::createKey(4096);

        file_put_contents($publicKey, (string)$key->getPublicKey());
        file_put_contents($privateKey, (string)$key);

        file_put_contents($xsPublicKey, (string)$xsKey->getPublicKey());
        file_put_contents($xsPrivateKey, (string)$xsKey);

        return Command::SUCCESS;
    }
}
