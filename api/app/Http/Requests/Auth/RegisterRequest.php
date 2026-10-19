<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        $ignore = $this->user()?->id;

        return [
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'required_without:phone', 'email', 'max:190', Rule::unique('users')->ignore($ignore)],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:30', Rule::unique('users')->ignore($ignore)],
            'password' => ['required', 'confirmed', Password::min(8)],
            'country' => ['nullable', 'string', 'size:2'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
