<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Content\Enums\ResourceCategory;
use App\Domain\Content\Models\Exercise;
use App\Domain\Content\Models\Guide;
use App\Domain\Content\Models\SupportResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\GuideResource;
use App\Http\Resources\SupportResourceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

/**
 * Contenus en libre accès : exercices, guides et ressources de proximité.
 *
 * @tags Contenus
 */
class ContentController extends Controller
{
    /**
     * Exercices guidés.
     *
     * Renvoyés en entier (étapes comprises) pour être enregistrés sur le téléphone et utilisés hors ligne.
     *
     * @unauthenticated
     */
    public function exercises(): AnonymousResourceCollection
    {
        return ExerciseResource::collection(Exercise::published()->get());
    }

    /** @unauthenticated */
    public function guides(): AnonymousResourceCollection
    {
        return GuideResource::collection(Guide::published()->get());
    }

    /** @unauthenticated */
    public function guide(string $slug): GuideResource
    {
        return new GuideResource(Guide::published()->where('slug', $slug)->firstOrFail());
    }

    /**
     * Ressources de proximité.
     *
     * Filtrables par ville et par catégorie ; `cities` liste les villes couvertes.
     *
     * @unauthenticated
     */
    public function resources(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'city' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', Rule::enum(ResourceCategory::class)],
            'country' => ['nullable', 'string', 'size:2'],
        ]);
        $country = $filters['country'] ?? config('sunoogo.default_country');
        $base = SupportResource::query()->where('is_active', true)->where('country', $country);

        return response()->json([
            'data' => SupportResourceResource::collection(
                (clone $base)
                    ->when($filters['city'] ?? null, fn ($q, $city) => $q->where('city', $city))
                    ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
                    ->orderBy('city')
                    ->orderBy('name')
                    ->get()
            ),
            'cities' => (clone $base)->distinct()->orderBy('city')->pluck('city'),
            'categories' => collect(ResourceCategory::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()]),
        ]);
    }
}
