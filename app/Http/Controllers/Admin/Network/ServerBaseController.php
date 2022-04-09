<?php

namespace App\Http\Controllers\Admin\Network;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ServerBaseController extends Controller
{

    public function all()
    {
        return Http::withToken(env('PANEL_ADMIN_KEY'))->get('https://' . env('PANEL_APP') . '/api/client')['data'];
    }

    public function get($id)
    {
        return Http::withToken(env('PANEL_ADMIN_KEY'))->get('https://' . env('PANEL_APP') . '/api/client/servers/' . $id . '/resources');
    }

    public function start($id) {
        return Http::withToken(env('PANEL_ADMIN_KEY'))->post('https://' . env('PANEL_APP') . '/api/client/servers/' . $id . '/power', [
            'signal' => 'start'
        ]);
    }
}
