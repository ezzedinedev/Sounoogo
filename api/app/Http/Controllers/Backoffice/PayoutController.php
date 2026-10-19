<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Consultation\Actions\ProcessPayout;
use App\Domain\Consultation\Enums\PayoutStatus;
use App\Domain\Consultation\Models\Payout;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Administration : traitement des demandes de versement des professionnels. */
class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        $status = PayoutStatus::tryFrom((string) $request->query('statut', PayoutStatus::Requested->value));

        return view('backoffice.payouts.index', [
            'status' => $status,
            'counts' => Payout::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'payouts' => Payout::query()
                ->with(['professional.user', 'processor'])
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(25)
                ->withQueryString(),
        ]);
    }

    public function update(Request $request, Payout $payout, ProcessPayout $process): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([PayoutStatus::Paid->value, PayoutStatus::Rejected->value])],
            'reference' => ['nullable', 'required_if:status,paid', 'string', 'max:60'],
            'note' => ['nullable', 'required_if:status,rejected', 'string', 'max:255'],
        ], [
            'reference.required_if' => 'Indique la référence de la transaction Mobile Money.',
            'note.required_if' => 'Explique la raison du refus au professionnel.',
        ]);

        $status = PayoutStatus::from($data['status']);
        $process($payout, $request->user(), $status, $data['reference'] ?? null, $data['note'] ?? null);

        return back()->with('status', $status === PayoutStatus::Paid ? 'Versement confirmé.' : 'Demande refusée.');
    }
}
