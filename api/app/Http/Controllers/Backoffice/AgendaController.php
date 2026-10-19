<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Consultation\Actions\CompleteBooking;
use App\Domain\Consultation\Actions\CreateSlotSeries;
use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Models\AvailabilitySlot;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Consultation\Services\VideoRooms;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/** Espace professionnel : créneaux et consultations. */
class AgendaController extends Controller
{
    public function index(Request $request, VideoRooms $rooms): View
    {
        $profile = $this->profile($request);
        $bookings = $profile->bookings()
            ->with(['slot', 'member', 'professional'])
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed])
            ->get();

        return view('backoffice.agenda.index', [
            'profile' => $profile,
            'rooms' => $rooms,
            'slots' => $profile->slots()->where('starts_at', '>', now())->orderBy('starts_at')->get()->groupBy(fn ($slot) => $slot->starts_at->toDateString()),
            // À venir : confirmées, de la plus proche à la plus lointaine. Historique : les 20 dernières terminées.
            'upcoming' => $bookings->where('status', BookingStatus::Confirmed)->sortBy(fn ($booking) => $booking->slot?->starts_at)->values(),
            'past' => $bookings->where('status', BookingStatus::Completed)->sortByDesc('completed_at')->take(20)->values(),
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

    public function storeSeries(Request $request, CreateSlotSeries $createSeries): RedirectResponse
    {
        $data = $request->validate([
            'from' => ['required', 'date', 'after_or_equal:today'],
            'weeks' => ['required', 'integer', 'between:1,8'],
            'weekdays' => ['required', 'array', 'min:1'],
            'weekdays.*' => ['integer', 'between:1,7'],
            'times' => ['required', 'array', 'min:1', 'max:10'],
            'times.*' => ['nullable', 'date_format:H:i'],
        ], [
            'weekdays.required' => 'Choisis au moins un jour.',
            'times.required' => 'Indique au moins une heure.',
        ]);

        $times = array_values(array_unique(array_filter($data['times'])));
        abort_if($times === [], 422, 'Indique au moins une heure.');

        $count = $createSeries(
            $this->profile($request),
            Carbon::parse($data['from']),
            (int) $data['weeks'],
            array_map('intval', $data['weekdays']),
            $times,
        );

        return back()->with('status', $count === 0
            ? 'Aucun créneau ajouté : ils existent déjà ou sont passés.'
            : trans_choice(':count créneau ajouté.|:count créneaux ajoutés.', $count));
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

    public function complete(Booking $booking, CompleteBooking $complete): RedirectResponse
    {
        $this->authorize('complete', $booking);
        $complete($booking);

        return back()->with('status', 'Consultation marquée comme terminée.');
    }

    /** Ouvre la salle de visio (le professionnel en est le modérateur). */
    public function video(Request $request, Booking $booking, VideoRooms $rooms): RedirectResponse
    {
        $this->authorize('join', $booking);

        return redirect()->away($rooms->join($booking->load(['slot', 'professional.user', 'member']), $request->user())->url);
    }

    private function profile(Request $request): ProfessionalProfile
    {
        return $request->user()->professionalProfile ?? abort(403, 'Profil professionnel introuvable.');
    }
}
