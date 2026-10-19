<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Listening\Enums\AlertStatus;
use App\Domain\Listening\Models\EmergencyAlert;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(Request $request): View
    {
        $status = AlertStatus::tryFrom((string) $request->query('statut'));

        return view('backoffice.alerts.index', [
            'alerts' => EmergencyAlert::query()
                ->with(['member', 'conversation.listener', 'handler'])
                ->when($status, fn ($q) => $q->where('status', $status), fn ($q) => $q->where('status', '!=', AlertStatus::Resolved))
                ->orderByRaw("case level when 'high' then 0 else 1 end")
                ->latest()
                ->paginate(25)
                ->withQueryString(),
            'status' => $status,
        ]);
    }

    public function acknowledge(Request $request, EmergencyAlert $alert): RedirectResponse
    {
        if ($alert->status === AlertStatus::Open) {
            $alert->update([
                'status' => AlertStatus::Acknowledged,
                'handled_by' => $request->user()->id,
                'acknowledged_at' => now(),
            ]);

            activity('supervision')->performedOn($alert)->log('Alerte prise en charge');
        }

        return back()->with('status', 'Alerte prise en charge.');
    }

    public function resolve(Request $request, EmergencyAlert $alert): RedirectResponse
    {
        $data = $request->validate(['notes' => ['required', 'string', 'max:2000']]);

        $alert->update([
            'status' => AlertStatus::Resolved,
            'handled_by' => $alert->handled_by ?? $request->user()->id,
            'acknowledged_at' => $alert->acknowledged_at ?? now(),
            'resolved_at' => now(),
            'notes' => $data['notes'],
        ]);

        activity('supervision')->performedOn($alert)->log('Alerte résolue');

        return back()->with('status', 'Alerte résolue.');
    }
}
