<?php

namespace App\Http\Requests\Listening;

use App\Domain\Listening\Enums\Topic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OpenConversationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'topic' => ['nullable', Rule::enum(Topic::class)],
            'channel' => ['nullable', 'string', 'in:chat,call'],
        ];
    }
}
