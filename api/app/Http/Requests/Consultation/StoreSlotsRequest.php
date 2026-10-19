<?php

namespace App\Http\Requests\Consultation;

use Illuminate\Foundation\Http\FormRequest;

class StoreSlotsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'slots' => ['required', 'array', 'min:1', 'max:50'],
            'slots.*.starts_at' => ['required', 'date', 'after:now'],
            'slots.*.ends_at' => ['required', 'date', 'after:slots.*.starts_at'],
        ];
    }
}
