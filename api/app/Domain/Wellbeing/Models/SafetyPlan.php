<?php

namespace App\Domain\Wellbeing\Models;

use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Plan de sécurité personnel, sur le modèle de Stanley et Brown : ce que le membre a préparé
 * pour traverser une crise. Lu uniquement par son auteur ; l'équipe n'y a pas accès.
 *
 * @property int $id
 * @property int $user_id
 * @property array<string, list<mixed>> $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'content'])]
class SafetyPlan extends Model
{
    /** Listes de textes libres. */
    public const TEXT_SECTIONS = ['warning_signs', 'coping_strategies', 'distractions', 'safe_environment', 'reasons_to_live'];

    /** Listes de contacts `{ name, phone }`. */
    public const CONTACT_SECTIONS = ['support_contacts', 'professional_contacts'];

    protected function casts(): array
    {
        return [
            'content' => 'encrypted:array',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<string, list<mixed>> Toutes les sections, vides si non renseignées. */
    public function sections(): array
    {
        return collect([...self::TEXT_SECTIONS, ...self::CONTACT_SECTIONS])
            ->mapWithKeys(fn (string $section) => [$section => $this->content[$section] ?? []])
            ->all();
    }
}
