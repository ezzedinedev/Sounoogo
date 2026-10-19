<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Domain\Listening\Enums\AlertStatus;
use App\Domain\Listening\Enums\ConversationStatus;
use App\Domain\Listening\Enums\ReportStatus;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\EmergencyAlert;
use App\Domain\Listening\Models\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isOneOf(Role::Professional)) {
            return redirect()->route('backoffice.agenda.index');
        }

        $weekAgo = now()->subWeek();
        $twoWeeksAgo = now()->subWeeks(2);

        $stats = [
            'waiting' => Conversation::where('status', ConversationStatus::Waiting)->count(),
            'active' => Conversation::where('status', ConversationStatus::Active)->count(),
            'open_alerts' => EmergencyAlert::where('status', '!=', AlertStatus::Resolved)->count(),
            'pending_reports' => Report::where('status', ReportStatus::Pending)->count(),
            'listeners_online' => User::role(Role::Listener->value)->where('is_available', true)->count(),
            'conversations_week' => Conversation::where('created_at', '>=', $weekAgo)->count(),
            'conversations_prev_week' => Conversation::whereBetween('created_at', [$twoWeeksAgo, $weekAgo])->count(),
            'members_week' => User::role(Role::Member->value)->where('created_at', '>=', $weekAgo)->count(),
            'members_prev_week' => User::role(Role::Member->value)->whereBetween('created_at', [$twoWeeksAgo, $weekAgo])->count(),
            'bookings_upcoming' => Booking::where('status', BookingStatus::Confirmed)->count(),
            'revenue_month' => (int) Payment::where('status', PaymentStatus::Succeeded)->where('paid_at', '>=', now()->startOfMonth())->sum('amount'),
            'revenue_prev_month' => (int) Payment::where('status', PaymentStatus::Succeeded)->whereBetween('paid_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->startOfMonth()])->sum('amount'),
        ];

        $days = collect(range(29, 0))->map(fn (int $offset) => now()->subDays($offset)->toDateString());
        $perDay = fn (string $model) => $model::query()
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('date(created_at) as day, count(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');
        $conversations = $perDay(Conversation::class);
        $alerts = $perDay(EmergencyAlert::class);

        return view('backoffice.dashboard', [
            'stats' => $stats,
            'chart' => [
                'labels' => $days->map(fn ($day) => Carbon::parse($day)->translatedFormat('j M'))->values(),
                'series' => [
                    ['name' => 'Conversations', 'data' => $days->map(fn ($day) => (int) ($conversations[$day] ?? 0))->values()],
                    ['name' => 'Alertes', 'data' => $days->map(fn ($day) => (int) ($alerts[$day] ?? 0))->values()],
                ],
            ],
            'alerts' => $user->can('alerts.manage')
                ? EmergencyAlert::with(['member', 'conversation.listener'])->where('status', '!=', AlertStatus::Resolved)->latest()->limit(6)->get()
                : collect(),
            'myConversations' => $user->listenerConversations()->with(['member', 'latestMessage'])->where('status', ConversationStatus::Active)->latest('updated_at')->get(),
        ]);
    }
}
