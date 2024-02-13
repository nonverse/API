<?php

namespace App\Http\Controllers\Proxy;

use App\Http\Controllers\Controller;
use App\Services\ApplicationProxyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LabsProxyController extends Controller
{
    /**
     * @var ApplicationProxyService
     */
    private ApplicationProxyService $proxyService;

    public function __construct(
        ApplicationProxyService $proxyService
    )
    {
        $this->proxyService = $proxyService;
    }

    public function forward(Request $request)
    {
        $url = 'https://labs.nonverse.test' . str_replace('/labs/', '/api/', $request->getRequestUri());

        if ($request->getMethod() === 'POST') {
            $response = Http::withToken($this->proxyService->createSignedToken('labs', $request->user()))->post($url, $request->input());
        } elseif ($request->getMethod() === 'GET') {
            $response = Http::withToken($this->proxyService->createSignedToken('labs', $request->user()))->get($url);
        }

        if ($response->clientError() || $response->serverError()) {
            return response($response->body(), $response->status());
        }
        return response($response->body())->withHeaders($response->headers());
    }
}
