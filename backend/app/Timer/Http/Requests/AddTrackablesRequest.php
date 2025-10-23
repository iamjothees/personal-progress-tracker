<?php

namespace App\Timer\Http\Requests;

use App\Models\TimerMatrix;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddTrackablesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'time_trackables' => 'required|array',
            'time_trackables.*.type' => 'required|in:'.implode(',', array_keys(TimerMatrix::timeTrackables())),
            'time_trackables.*.id' => [
                'required', 'integer', 
                function (string $attribute, mixed $value, Closure $fail) {
                    $type = $this->input(str($attribute)->replace('.id', '.type'));
                    $class = TimerMatrix::timeTrackables()[$type]['class'] ?? null;
                    if ( $class === null || (!$class::query()->whereId($value)->exists())) {
                        $fail("The {$attribute} is invalid.");
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'time_trackables' => "Trackables must be an array of objects with type and id keys.",
            'time_trackables.*.type' => "Invalid time_trackables type",
            'time_trackables.*.id' => "Invalid time_trackables id",
        ];
    }
}
