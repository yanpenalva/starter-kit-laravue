<?php

declare(strict_types = 1);

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

trait FailedValidation {
    protected function failedValidation(Validator $validator): HttpResponseException {
        throw new HttpResponseException(
            response()->json(
                ['errors' => $validator->errors()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            )
        );
    }
}
