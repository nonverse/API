<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Notifications\OneTimePassword;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SendPasswordService
{
    /**
     * Send a One Time Password to a user's in game chat and store
     * Hash and expiry in encrypted session store
     *
     * @param Request $request
     * @param $username
     * @param $identifier
     * @return bool
     */
    public function chat(Request $request, $username, $identifier): bool
    {
        // Generate a random 12 character One Time Password
        $otp = Str::random(12);

        // Create a session store containing a password hash and expiry
        $request->session()->put('one_time_password', [
            'uuid' => $request->user()->uuid,
            'mc_username' => $username,
            'password' => Hash::make($otp),
            'password_identifier' => $identifier,
            'password_expiry' => CarbonImmutable::now()->addMinutes(5)
        ]);

        // Send a command to the panel to display the one time password in the user's in game chat
        $command = Http::withToken(env('PANEL_ADMIN_KEY'))->post('https://' . env('PANEL_APP') . '/api/client/servers/' . env('MINECRAFT_LOBBY_SERVER') . '/command', [
            'command' => 'tellraw ' . $username . ' ["",{"text":"Your Nonverse one time password is ","bold":true,"color":"gold"},{"text":"' . $otp . '","underlined":true,"color":"blue"},"\n",{"text":"This password will expire in 5 minutes","color":"red"},"\n",{"text":"If you did not recently request a password, you can safely ignore this message","bold":true,"color":"aqua","insertion":"If you did recently make a request to link your profile, you can safely ignore this message"}]'
        ]);

        // If the command fails, remove the session store and return a HTTP error 500
        if ($command->status() !== 204) {
            $request->session()->forget('one_time_password');
            return false;
        }

        return true;
    }

    /**
     * Send a One Time Password to a user's in email and store
     * Hash and expiry in encrypted session store
     *
     * @param Request $request
     * @param $identifier
     * @return bool
     */
    public function email(Request $request, $identifier): bool
    {
        $otp = Str::random(12);
        $user = $request->user();

        // Create a session store containing a password hash and expiry
        $request->session()->put('one_time_password', [
            'uuid' => $user->uuid,
            'password' => Hash::make($otp),
            'password_identifier' => $identifier,
            'password_expiry' => CarbonImmutable::now()->addMinutes(5)
        ]);

        try {
            $user->notify(new OneTimePassword($user, [
                'value' => $otp,
                'request_time' => CarbonImmutable::now(),
                'request_ip' => $_SERVER['REMOTE_ADDR']
            ]));
        } catch (Exception $e) {
            $request->session()->forget('one_time_password');
            return false;
        }

        return true;
    }
}
