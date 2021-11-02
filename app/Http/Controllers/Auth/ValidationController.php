<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidationController extends Controller
{

    /**
     * Check if a request email is already in use by a registered user
     *
     * @param Request $request
     *
     * @return Response
     */
    public function validateNewEmail(Request $request): Response
    {
        $rule = array('email' => 'unique:users,email');
        $validator = Validator::make($request->all(), $rule);

        return $validator->fails()
            ? response('email exists', 422)
            : response('email available', 200);
    }

}
