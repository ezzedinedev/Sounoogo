<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RecoverRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pseudonym' => ['required', 'string', 'max:60'],
            'recovery_code' => ['required', 'string', 'max:30'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
