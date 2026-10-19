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
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isOneOf(Role::Professional)) {
            return redirect()->route('backoffice.agenda.index');
        }

        $stats = [
            'waiting' => Conversation::where('status', ConversationStatus::Waiting)->count(),
            'active' => Conversation::where('status', ConversationStatus::Active)->count(),
            'open_alerts' => EmergencyAlert::where('status', '!=', AlertStatus::Resolved)->count(),
            'pending_reports' => Report::where('status', ReportStatus::Pending)->count(),
            'listeners_online' => User::role(Role::Listener->value)->where('is_available', true)->count(),
            'members_week' => User::role(Role::Member->value)->where('created_at', '>=', now()->subWeek())->count(),
            'bookings_upcoming' => Booking::where('status', BookingStatus::Confirmed)->count(),
            'revenue_month' => (int) Payment::where('status', PaymentStatus::Succeeded)->where('paid_at', '>=', now()->startOfMonth())->sum('amount'),
        ];

        $conversationsPerDay = Conversation::query()
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->select(DB::raw('date(created_at) as day'), DB::raw('count(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day');

        $days = collect(range(13, 0))->map(fn (int $offset) => now()->subDays($offset)->toDateString());

        return view('backoffice.dashboard', [
            'stats' => $stats,
            'chart' => [
                'labels' => $days->map(fn ($day) => Carbon::parse($day)->translatedFormat('j M'))->values(),
                'values' => $days->map(fn ($day) => (int) ($conversationsPerDay[$day] ?? 0))->values(),
            ],
            'alerts' => $user->can('alerts.manage')
                ? EmergencyAlert::with('conversation')->where('status', '!=', AlertStatus::Resolved)->latest()->limit(5)->get()
                : collect(),
            'myConversations' => $user->listenerConversations()->with('latestMessage')->where('status', ConversationStatus::Active)->latest('updated_at')->get(),
        ]);
    }
}
