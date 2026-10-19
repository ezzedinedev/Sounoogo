<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Domain\Listening\Enums\AlertStatus;
use App\Domain\Listening\Enums\ConversationStatus;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\EmergencyAlert;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Poste de supervision : file d'attente, écoutes en cours, alertes et indicateurs du jour,
 * rafraîchis en temps réel.
 */
class SupervisionController extends Controller
{
    public function index(): View
    {
        return view('backoffice.supervision.index', $this->state());
    }

    /** Fragment rafraîchi par la page (événements temps réel et filet de 15 s). */
    public function live(): View
    {
        return view('backoffice.supervision.partials.live', $this->state());
    }

    /** @return array<string, mixed> */
    private function state(): array
    {
        $today = Conversation::query()->where('created_at', '>=', today())->get(['created_at', 'first_response_at']);
        $responseTimes = $today
            ->filter(fn (Conversation $c) => $c->first_response_at !== null)
            ->map(fn (Conversation $c) => $c->created_at->diffInSeconds($c->first_response_at))
            ->sort()
            ->values();

        return [
            'waiting' => Conversation::query()
                ->where('status', ConversationStatus::Waiting)
                ->with('member')
                ->orderByRaw("case risk_level when 'high' then 0 when 'medium' then 1 else 2 end")
                ->oldest()
                ->get(),
            'active' => Conversation::query()
                ->where('status', ConversationStatus::Active)
                ->with(['member', 'listener'])
                ->orderByRaw("case risk_level when 'high' then 0 when 'medium' then 1 else 2 end")
                ->latest('updated_at')
                ->get(),
            'alerts' => EmergencyAlert::query()
                ->where('status', '!=', AlertStatus::Resolved)
                ->with(['member', 'conversation.listener', 'handler'])
                ->orderByRaw("case level when 'high' then 0 else 1 end")
                ->latest()
                ->get(),
            'listeners' => User::role([Role::Listener->value, Role::Supervisor->value])
                ->where('is_available', true)
                ->withCount(['listenerConversations as active_count' => fn ($q) => $q->where('status', ConversationStatus::Active)])
                ->orderBy('name')
                ->get(),
            'today' => [
                'conversations' => $today->count(),
                'median_response' => $responseTimes->isEmpty() ? null : (int) $responseTimes->median(),
                'alerts' => EmergencyAlert::where('created_at', '>=', today())->count(),
                'resolved' => EmergencyAlert::where('resolved_at', '>=', today())->count(),
            ],
        ];
    }
}
