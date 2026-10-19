<?php

namespace App\Http\Requests\Wellbeing;

use App\Domain\Wellbeing\Models\SafetyPlan;
use Illuminate\Foundation\Http\FormRequest;

class SaveSafetyPlanRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [];

        foreach (SafetyPlan::TEXT_SECTIONS as $section) {
            $rules[$section] = ['present', 'array', 'max:10'];
            // Une ligne laissée vide est ignorée à l'enregistrement.
            $rules["{$section}.*"] = ['nullable', 'string', 'max:200'];
        }

        foreach (SafetyPlan::CONTACT_SECTIONS as $section) {
            $rules[$section] = ['present', 'array', 'max:5'];
            $rules["{$section}.*.name"] = ['required', 'string', 'max:80'];
            $rules["{$section}.*.phone"] = ['required', 'string', 'max:30'];
        }

        return $rules;
    }
}
