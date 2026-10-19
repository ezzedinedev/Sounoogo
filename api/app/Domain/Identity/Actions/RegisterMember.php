<?php

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;

final class RegisterMember
{
    /**
     * Crée un compte avec identifiants, ou complète le compte anonyme en cours
     * (l'historique est conservé, le pseudonyme reste l'identité visible).
     */
    public function __invoke(array $data, ?User $anonymous = null): User
    {
        $attributes = [
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'is_anonymous' => false,
            'recovery_code_hash' => null,
        ];

        if ($anonymous?->is_anonymous) {
            $anonymous->update($attributes);

            return $anonymous;
        }

        $user = User::create([
            ...$attributes,
            'pseudonym' => User::generatePseudonym(),
            'country' => $data['country'] ?? config('sunoogo.default_country'),
        ]);

        $user->assignRole(Role::Member->value);

        return $user;
    }
}
