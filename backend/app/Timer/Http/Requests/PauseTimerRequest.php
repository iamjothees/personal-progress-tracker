<?php

namespace App\Timer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PauseTimerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'has_latest_activity' => 'boolean',
            'same_latest_activity' => 'accepted_if:has_latest_activity,true',
        ];
    }

    public function messages(): array
    {
        return [
            'same_latest_activity' => 'Latest activity does not match',
        ];
    }

    protected function prepareForValidation(): void
    {
        $timer = $this->route('timer');

        $this->merge([
            'has_latest_activity' => (bool) $timer->latestActivity,
            'same_latest_activity' => $timer->latestActivity?->id === $this->input('latest_activity_id'),
        ]);
    }
}
