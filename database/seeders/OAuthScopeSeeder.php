<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OAuthScopeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->scopes as $key => $value) {
            DB::table('oauth_scopes')->insert([
                'id' => $key,
                'description' => $value,
                'client_id' => array_key_exists($key, $this->clientOnly) ? $this->clientOnly[$key] : null,
                'created_at' => CarbonImmutable::now(),
                'updated_at' => CarbonImmutable::now()
            ]);
        }
    }

    /**
     * @var array|string[]
     *
     * OAuth scopes and their description
     */
    protected array $scopes = [
        // Restricted
        'user.*' => 'Have full access to view and edit your account',
        'labs.*' => 'Have full access to your Nonverse Labs data',

        // Unrestricted
        'user.store.read' => 'View your basic info',
        'user.settings.read' => 'View your application settings',
    ];

    /**
     * @var array|string[]
     *
     * OAuth scopes that should be restricted to a specific client
     * and the client ID that they should be restricted to
     */
    protected array $clientOnly = [
        '*' => 'a01776de-e24a-4f2a-bedc-595bf67225a8'
    ];
}
