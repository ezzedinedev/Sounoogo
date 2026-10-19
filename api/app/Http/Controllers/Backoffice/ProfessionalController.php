<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Consultation\Enums\ConsultationMode;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfessionalController extends Controller
{
    public function index(): View
    {
        return view('backoffice.professionals.index', [
            'professionals' => ProfessionalProfile::with('user')
                ->withCount(['bookings', 'slots' => fn ($q) => $q->open()])
                ->latest()
                ->paginate(25),
        ]);
    }

    public function create(Request $request): View
    {
        return view('backoffice.professionals.form', [
            'professional' => new ProfessionalProfile(['user_id' => $request->query('user'), 'session_minutes' => 50, 'modes' => []]),
            'candidates' => $this->candidates(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $professional = ProfessionalProfile::create($this->validated($request, creating: true));

        activity('admin')->performedOn($professional)->log('Profil professionnel créé');

        return redirect()->route('backoffice.professionals.index')->with('status', 'Profil professionnel créé.');
    }

    public function edit(ProfessionalProfile $professional): View
    {
        return view('backoffice.professionals.form', ['professional' => $professional->load('user'), 'candidates' => collect()]);
    }

    public function update(Request $request, ProfessionalProfile $professional): RedirectResponse
    {
        $professional->update($this->validated($request));

        activity('admin')->performedOn($professional)->log('Profil professionnel modifié');

        return redirect()->route('backoffice.professionals.index')->with('status', 'Profil mis à jour.');
    }

    public function toggleVerification(ProfessionalProfile $professional): RedirectResponse
    {
        $professional->update(['verified_at' => $professional->verified_at ? null : now()]);

        activity('admin')->performedOn($professional)->log($professional->verified_at ? 'Professionnel vérifié' : 'Vérification retirée');

        return back()->with('status', $professional->verified_at ? 'Professionnel vérifié : il apparaît dans l\'annuaire.' : 'Vérification retirée.');
    }

    private function validated(Request $request, bool $creating = false): array
    {
        $data = $request->validate([
            'user_id' => $creating ? ['required', Rule::in($this->candidates()->pluck('id'))] : ['prohibited'],
            'title' => ['required', 'string', 'max:80'],
            'specialty' => ['required', 'string', 'max:80'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'city' => ['nullable', 'string', 'max:60'],
            'license_number' => ['nullable', 'string', 'max:60'],
            'session_price' => ['required', 'integer', 'min:0', 'max:1000000'],
            'session_minutes' => ['required', 'integer', 'min:15', 'max:180'],
            'modes' => ['required', 'array', 'min:1'],
            'modes.*' => [Rule::enum(ConsultationMode::class)],
            'languages' => ['nullable', 'string', 'max:200'],
            'is_active' => ['boolean'],
        ]);

        $data['languages'] = collect(explode(',', $data['languages'] ?? ''))->map(fn ($l) => trim($l))->filter()->values()->all();
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /** Comptes professionnels sans profil. */
    private function candidates()
    {
        return User::role(Role::Professional->value)->whereDoesntHave('professionalProfile')->orderBy('name')->get();
    }
}
