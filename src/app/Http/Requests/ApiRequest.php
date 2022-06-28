<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

abstract class ApiRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        Log::debug($validator->errors()->first());
        throw new HttpResponseException(response()->json(['message' => $validator->errors()->first()], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
