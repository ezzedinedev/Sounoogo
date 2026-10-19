<?php

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Niveau 1 : accès sans aucune donnée identifiante. Le membre reçoit un code de
 * récupération (montré une seule fois) pour retrouver son compte sur un autre appareil.
 */
final class CreateAnonymousMember
{
    /** @return array{0: User, 1: string} Le membre et son code de récupération en clair. */
    public function __invoke(?string $country = null, ?string $locale = null): array
    {
        $recoveryCode = Str::upper(implode('-', str_split(Str::random(16), 4)));

        $user = User::create([
            'pseudonym' => User::generatePseudonym(),
            'is_anonymous' => true,
            'recovery_code_hash' => Hash::make($recoveryCode),
            'country' => $country ?? config('sunoogo.default_country'),
            'locale' => $locale ?? 'fr',
        ]);

        $user->assignRole(Role::Member->value);

        return [$user, $recoveryCode];
    }
}
