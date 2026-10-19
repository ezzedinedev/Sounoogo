<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Models\AvailabilitySlot;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Consultation\StoreSlotsRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\SlotResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * Espace professionnel : créneaux et consultations.
 *
 * @tags Espace professionnel
 */
class ProfessionalAgendaController extends Controller
{
    public function slots(Request $request): AnonymousResourceCollection
    {
        return SlotResource::collection(
            $this->profile($request)->slots()->where('starts_at', '>', now()->subDay())->orderBy('starts_at')->get()
        );
    }

    public function storeSlots(StoreSlotsRequest $request): JsonResponse
    {
        $slots = $this->profile($request)->slots()->createMany($request->validated('slots'));

        return SlotResource::collection($slots)->response()->setStatusCode(201);
    }

    public function destroySlot(Request $request, AvailabilitySlot $slot): Response
    {
        abort_unless($slot->professional_profile_id === $this->profile($request)->id, 404);
        abort_if($slot->is_booked, 409, 'Ce créneau est réservé : annule d\'abord la consultation.');

        $slot->delete();

        return response()->noContent();
    }

    public function bookings(Request $request): AnonymousResourceCollection
    {
        return BookingResource::collection(
            $this->profile($request)->bookings()
                ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed])
                ->with(['slot', 'member'])
                ->latest()
                ->paginate(20)
        );
    }

    public function complete(Booking $booking): BookingResource
    {
        $this->authorize('complete', $booking);
        abort_unless($booking->status === BookingStatus::Confirmed, 409, 'Seule une consultation confirmée peut être terminée.');

        $booking->update(['status' => BookingStatus::Completed, 'completed_at' => now()]);

        return new BookingResource($booking->load(['slot', 'member']));
    }

    private function profile(Request $request): ProfessionalProfile
    {
        return $request->user()->professionalProfile ?? abort(403, 'Profil professionnel introuvable.');
    }
}
