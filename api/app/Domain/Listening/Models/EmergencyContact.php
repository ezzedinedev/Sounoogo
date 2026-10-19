<?php

namespace App\Domain\Listening\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $country
 * @property string $label
 * @property string $phone
 * @property string|null $description
 * @property int $position
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['country', 'label', 'phone', 'description', 'position', 'is_active'])]
class EmergencyContact extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
