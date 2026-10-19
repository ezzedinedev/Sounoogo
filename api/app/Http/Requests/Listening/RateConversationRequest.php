<?php

namespace App\Http\Requests\Listening;

use Illuminate\Foundation\Http\FormRequest;

class RateConversationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
        ];
    }
}
