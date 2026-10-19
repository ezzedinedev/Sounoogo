<?php

namespace App\Http\Requests\Consultation;

use App\Domain\Consultation\Enums\ConsultationMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookConsultationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'slot_id' => ['required', 'integer', 'exists:availability_slots,id'],
            'mode' => ['required', Rule::enum(ConsultationMode::class)],
            'keep_anonymous' => ['boolean'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
