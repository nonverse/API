<?php

namespace App\Exceptions\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidationException extends HttpException
{
    private \Illuminate\Validation\Validator $validator;

    public function __construct($validator) {
        parent::__construct(422);
        $this->validator = $validator;
    }

    public function getResponse(): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'errors' => $this->validator->errors()
        ], $this->getStatusCode());
    }

    public function render(): JsonResponse
    {
        return $this->getResponse();
    }
}
