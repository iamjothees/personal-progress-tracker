<?php

namespace App\Timer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResumeTimerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'timer_activity' => 'accepted',
            'same_latest_activity' => 'accepted',
            'seconds_elapsed' => ['nullable', 'integer']
        ];
    }

    public function messages(): array
    {
        return [
            'timer_activity' => 'Timer activity does not belong to this timer.',
            'same_latest_activity' => 'Latest activity does not match',
            'seconds_elapsed' => 'Invalid format',
        ];
    }

    protected function prepareForValidation(): void
    {
        $timer = $this->route('timer');
        $timerActivity = $this->route('timerActivity');

        $this->merge([
            'timer_activity' => $timerActivity->timer_id === $timer->id,
            'same_latest_activity' => $timerActivity->id === $timer->latestActivity?->id,
        ]);
    }
}
