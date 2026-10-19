<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Consultation\Actions\RequestPayout;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Consultation\Services\ProfessionalEarnings;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Espace professionnel : revenus et demandes de versement. */
class EarningsController extends Controller
{
    public function index(Request $request, ProfessionalEarnings $earnings): View
    {
        $profile = $this->profile($request);

        return view('backoffice.earnings.index', [
            'profile' => $profile,
            'summary' => $earnings->summary($profile),
            'monthly' => $earnings->monthly($profile),
            'payouts' => $profile->payouts()->latest()->limit(20)->get(),
            'commission' => (int) config('sunoogo.payouts.commission_percent'),
            'minimum' => (int) config('sunoogo.payouts.minimum_amount'),
        ]);
    }

    public function store(Request $request, RequestPayout $requestPayout): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9 ]{8,20}$/'],
        ], [
            'phone.regex' => 'Numéro Mobile Money invalide.',
        ]);

        $requestPayout($this->profile($request), (int) $data['amount'], preg_replace('/\s+/', '', $data['phone']) ?? $data['phone']);

        return back()->with('status', 'Demande de versement envoyée. Elle sera traitée par l\'équipe SUNOOGO.');
    }

    private function profile(Request $request): ProfessionalProfile
    {
        return $request->user()->professionalProfile ?? abort(403, 'Profil professionnel introuvable.');
    }
}
