<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Content\Enums\ResourceCategory;
use App\Domain\Content\Models\SupportResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupportResourceController extends Controller
{
    public function index(Request $request): View
    {
        return view('backoffice.resources.index', [
            'resources' => SupportResource::query()
                ->when($request->query('ville'), fn ($q, $city) => $q->where('city', $city))
                ->orderBy('city')->orderBy('name')
                ->paginate(30)
                ->withQueryString(),
            'cities' => SupportResource::query()->distinct()->orderBy('city')->pluck('city'),
        ]);
    }

    public function create(): View
    {
        return view('backoffice.resources.form', ['resource' => new SupportResource(['category' => ResourceCategory::HealthCenter])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $resource = SupportResource::create($this->validated($request));
        activity('contenus')->performedOn($resource)->log('Ressource ajoutée');

        return redirect()->route('backoffice.resources.index')->with('status', 'Ressource ajoutée à l\'annuaire.');
    }

    public function edit(SupportResource $resource): View
    {
        return view('backoffice.resources.form', ['resource' => $resource]);
    }

    public function update(Request $request, SupportResource $resource): RedirectResponse
    {
        $resource->update($this->validated($request));
        activity('contenus')->performedOn($resource)->log('Ressource modifiée');

        return redirect()->route('backoffice.resources.index')->with('status', 'Ressource enregistrée.');
    }

    public function destroy(SupportResource $resource): RedirectResponse
    {
        $resource->delete();
        activity('contenus')->log("Ressource « {$resource->name} » supprimée");

        return redirect()->route('backoffice.resources.index')->with('status', 'Ressource supprimée.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', Rule::enum(ResourceCategory::class)],
            'city' => ['required', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'opening_hours' => ['nullable', 'string', 'max:160'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'is_free' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        return [...$data, 'city' => trim($data['city']), 'is_free' => $request->boolean('is_free'), 'is_active' => $request->boolean('is_active')];
    }
}
