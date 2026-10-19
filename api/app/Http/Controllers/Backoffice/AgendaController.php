<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Models\AvailabilitySlot;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/** Espace professionnel : créneaux et consultations. */
class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $this->profile($request);

        return view('backoffice.agenda.index', [
            'profile' => $profile,
            'slots' => $profile->slots()->where('starts_at', '>', now())->orderBy('starts_at')->get()->groupBy(fn ($slot) => $slot->starts_at->toDateString()),
            'bookings' => $profile->bookings()
                ->with(['slot', 'member'])
                ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed])
                ->latest()
                ->limit(30)
                ->get(),
        ]);
    }

    public function storeSlot(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
        ]);

        $profile = $this->profile($request);
        $start = Carbon::parse("{$data['date']} {$data['time']}");

        abort_if($start->isPast(), 422, 'Ce créneau est déjà passé.');

        $profile->slots()->create(['starts_at' => $start, 'ends_at' => $start->copy()->addMinutes($profile->session_minutes)]);

        return back()->with('status', 'Créneau ajouté.');
    }

    public function destroySlot(Request $request, AvailabilitySlot $slot): RedirectResponse
    {
        abort_unless($slot->professional_profile_id === $this->profile($request)->id, 404);

        if ($slot->is_booked) {
            return back()->with('error', 'Ce créneau est réservé : il ne peut pas être supprimé.');
        }

        $slot->delete();

        return back()->with('status', 'Créneau supprimé.');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        $this->authorize('complete', $booking);

        if ($booking->status === BookingStatus::Confirmed) {
            $booking->update(['status' => BookingStatus::Completed, 'completed_at' => now()]);
        }

        return back()->with('status', 'Consultation marquée comme terminée.');
    }

    private function profile(Request $request): ProfessionalProfile
    {
        return $request->user()->professionalProfile ?? abort(403, 'Profil professionnel introuvable.');
    }
}
