<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Exception;

class EmailVerificationController extends Controller
{

    /**
     * Verify an incoming verification request containing an user specific ID and Hash
     *
     * @param EmailVerificationRequest $request
     * @return Application|RedirectResponse|Redirector
     */
    public function verify(EmailVerificationRequest $request)
    {

        // Laravel will automatically handle Email verification and \fulfill the request
        $request->fulfill();

        return redirect('http://' . env('BASE_APP'));
    }

    /**
     * (Re)send the email verification notification to the currently authenticated user
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function resend(Request $request)
    {

        $user = $request->user();

        // Try sending the notification
        try {
            $user->sendEmailVerificationNotification();
        } catch (Exception $e) {
            return response('Email not sent', 500);
        }

        return new JsonResponse([
            'data' => [
                'complete' => true,
                'email' => $user->email
            ]
        ]);
    }
}
