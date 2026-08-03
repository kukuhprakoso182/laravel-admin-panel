<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

abstract class BaseFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        // Request AJAX/fetch (mengirim header Accept: application/json)
        if ($this->wantsJson() || $this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Data yang dikirim tidak valid.',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        // Form Blade biasa: kembalikan ke behavior default Laravel
        // (redirect back dengan $errors bag + old input)
        throw new ValidationException($validator, $this->redirector
            ->back()
            ->withInput($this->except($this->dontFlash))
            ->withErrors($validator->errors(), $this->errorBag)
        );
    }
}
