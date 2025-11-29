<?php

namespace App\Timer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StopTimerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seconds_elapsed' => ['nullable', 'integer'] // from start
        ];
    }

    public function messages(): array
    {
        return [
            'seconds_elapsed' => 'Invalid format',
        ];
    }
}
