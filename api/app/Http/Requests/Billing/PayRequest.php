<?php

namespace App\Http\Requests\Billing;

use App\Domain\Billing\Enums\PaymentProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'provider' => ['required', Rule::enum(PaymentProvider::class)],
            // Numéro Mobile Money du payeur : transmis au prestataire, jamais enregistré.
            'phone' => [
                Rule::requiredIf(fn () => PaymentProvider::tryFrom((string) $this->input('provider'))?->requiresPhone() ?? false),
                'nullable', 'string', 'regex:/^\+?[0-9]{8,15}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Indique le numéro Mobile Money à débiter.',
            'phone.regex' => 'Ce numéro de téléphone ne semble pas valide.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('phone'))) {
            $this->merge(['phone' => preg_replace('/[\s.\-]/', '', $this->input('phone'))]);
        }
    }

    public function provider(): PaymentProvider
    {
        return PaymentProvider::from($this->validated('provider'));
    }

    public function phone(): ?string
    {
        return $this->validated('phone');
    }
}
