<?php

namespace App\Services\Services\Minecraft;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MinecraftAuthService
{
    /**
     * Validate a username against Mojangs servers
     *
     * @param string $username
     * @return bool
     */
    public function validateUsername(string $username): bool
    {

        $response = json_decode(Http::get('https://api.mojang.com/users/profiles/minecraft/' . $username), true);

        if (!array_key_exists('id', $response)) {
            return false;
        }

        return $response['id'];
    }

    public function sendVerificationCode(string $username)
    {
        $code = Str::random(6);

        $request = Http::withToken(env('MC_SERVER_TOKEN'))->post(env('PANEL_SERVER') . '/api/client/servers/' . substr(env('MC_SERVER_IDENTIFIER'), 0, 8) . '/command', [
            'command' => '/tellraw @p ["",{"text":"Your Nonverse account verification code is ","color":"white"},{"text":"242343","color":"gold"}]'
        ]);
    }
}
