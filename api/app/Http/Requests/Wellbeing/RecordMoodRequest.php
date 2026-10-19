<?php

namespace App\Http\Requests\Wellbeing;

use App\Domain\Wellbeing\Enums\Feeling;
use App\Domain\Wellbeing\Enums\Mood;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordMoodRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mood' => ['required', Rule::enum(Mood::class)],
            'feelings' => ['array', 'max:5'],
            'feelings.*' => ['distinct', Rule::enum(Feeling::class)],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function mood(): Mood
    {
        return Mood::from((int) $this->validated('mood'));
    }

    /** @return list<Feeling> */
    public function feelings(): array
    {
        return array_map(Feeling::from(...), $this->validated('feelings', []));
    }
}
