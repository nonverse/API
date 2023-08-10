<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->actions as $key => $value) {
            DB::table('actions')->insert([
                'id' => $key,
                'description' => $value,
                'created_at' => CarbonImmutable::now(),
                'updated_at' => CarbonImmutable::now()
            ]);
        }
    }

    /**
     * @var array|string[]
     *
     * Actions that can be authorized and their
     * descriptions
     */
    protected array $actions = [
        'update_email' => 'Update E-Mail',
        'update_phone' => 'Update phone number',
        'update_recovery_phone' => 'Update recovery phone number',
        'update_recovery_email' => 'Update recovery E-Mail',
        'update_password' => 'Update password',
        'update_pin' => 'Update pin',
        'update_two_step_login' => 'Update Two-Step login'
    ];
}
