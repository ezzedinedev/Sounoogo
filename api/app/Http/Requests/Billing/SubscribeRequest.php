<?php

namespace App\Http\Requests\Billing;

use Illuminate\Validation\Rule;

class SubscribeRequest extends PayRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'plan' => ['required', 'string', Rule::exists('plans', 'code')->where('is_active', true)],
        ];
    }
}
