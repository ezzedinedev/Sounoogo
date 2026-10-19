<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfessionalResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @tags Consultations
 */
class ProfessionalController extends Controller
{
    /**
     * Annuaire des professionnels vérifiés.
     *
     * Filtres : `filter[specialty]`, `filter[city]`, `filter[mode]`. Tri : `sort=session_price`.
     *
     * @unauthenticated
     */
    public function index(): AnonymousResourceCollection
    {
        $professionals = QueryBuilder::for(ProfessionalProfile::query()->listed()->with('user'))
            ->allowedFilters(
                AllowedFilter::partial('specialty'),
                AllowedFilter::exact('city'),
                AllowedFilter::callback('mode', fn ($query, $mode) => $query->whereJsonContains('modes', $mode)),
            )
            ->allowedSorts('session_price', 'created_at')
            ->defaultSort('session_price')
            ->paginate(20)
            ->withQueryString();

        return ProfessionalResource::collection($professionals);
    }

    /**
     * Fiche d'un professionnel et ses prochains créneaux libres.
     *
     * @unauthenticated
     */
    public function show(ProfessionalProfile $professional): ProfessionalResource
    {
        abort_unless($professional->is_active && $professional->verified_at !== null, 404);

        return new ProfessionalResource($professional->load([
            'user',
            'slots' => fn ($query) => $query->open()->orderBy('starts_at')->limit(30),
        ]));
    }
}
