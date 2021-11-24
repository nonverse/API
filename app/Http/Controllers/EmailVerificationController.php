<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{

    /**
     * Verify an incoming verification request containing an user specific ID and Hash
     *
     * @param EmailVerificationRequest $request
     */
    public function verify(EmailVerificationRequest $request) {

        // Laravel will automatically handle Email verification and \fulfill the request
        $request->fulfill();

        return redirect('http://' . env('BASE_APP'));
    }

}
