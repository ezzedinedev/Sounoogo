<?php

namespace App\Http\Requests\Listening;

use App\Domain\Listening\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reason' => ['required', Rule::in(array_keys(Report::REASONS))],
            'details' => ['nullable', 'string', 'max:2000'],
            'message_id' => ['nullable', 'integer', Rule::exists('messages', 'id')->where('conversation_id', $this->route('conversation')->id)],
        ];
    }
}
