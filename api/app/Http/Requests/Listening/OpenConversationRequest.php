<?php

namespace App\Http\Requests\Listening;

use Illuminate\Foundation\Http\FormRequest;

class OpenConversationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'topic' => ['nullable', 'string', 'in:stress,anxiety,sadness,relationship,family,grief,violence,self_esteem,other'],
            'channel' => ['nullable', 'string', 'in:chat,call'],
        ];
    }
}
