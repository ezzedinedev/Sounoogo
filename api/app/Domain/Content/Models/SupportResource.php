<?php

namespace App\Domain\Content\Models;

use App\Domain\Content\Enums\ResourceCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Ressource de proximité (centre de santé, association, aide juridique…), vérifiée par l'équipe.
 *
 * @property int $id
 * @property string $name
 * @property ResourceCategory $category
 * @property string $country
 * @property string $city
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $website
 * @property string|null $description
 * @property string|null $opening_hours
 * @property bool $is_free
 * @property float|null $latitude
 * @property float|null $longitude
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'category', 'country', 'city', 'address', 'phone', 'website', 'description', 'opening_hours', 'is_free', 'latitude', 'longitude', 'is_active'])]
class SupportResource extends Model
{
    protected $attributes = [
        'country' => 'BF',
        'is_free' => false,
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'category' => ResourceCategory::class,
            'is_free' => 'boolean',
            'is_active' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }
}
