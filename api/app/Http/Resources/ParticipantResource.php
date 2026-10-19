<?php

namespace App\Http\Resources;

use App\Domain\Identity\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Ce qu'un participant voit de l'autre : un nom d'affichage, jamais d'identifiant de contact.
 *
 * @mixin User
 */
class ParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'display_name' => $this->displayName(),
            'role' => $this->primaryRole()?->value,
        ];
    }
}
