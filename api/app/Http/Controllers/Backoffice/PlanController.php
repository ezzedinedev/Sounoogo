<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Billing\Models\Plan;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('backoffice.plans.index', [
            'plans' => Plan::withCount(['subscriptions' => fn ($q) => $q->where('status', 'active')])->orderBy('price')->get(),
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0', 'max:1000000'],
            'consultation_discount' => ['required', 'integer', 'between:0,100'],
            'features' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
        ]);

        $plan->update([
            ...$data,
            'features' => collect(preg_split('/\R/', $data['features'] ?? ''))->map(fn ($f) => trim($f))->filter()->values()->all(),
            'is_active' => $request->boolean('is_active'),
        ]);

        activity('admin')->performedOn($plan)->log('Offre modifiée');

        return back()->with('status', "Offre « {$plan->name} » enregistrée.");
    }
}
