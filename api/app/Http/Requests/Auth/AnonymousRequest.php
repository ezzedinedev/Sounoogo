<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AnonymousRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'country' => ['nullable', 'string', 'size:2'],
            'locale' => ['nullable', 'string', 'in:fr,en'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
