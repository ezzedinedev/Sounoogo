<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Billing\Actions\InitiatePayment;
use App\Domain\Consultation\Actions\BookConsultation;
use App\Domain\Consultation\Actions\CancelBooking;
use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Enums\ConsultationMode;
use App\Domain\Consultation\Models\AvailabilitySlot;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Consultation\Services\VideoRooms;
use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\PayRequest;
use App\Http\Requests\Consultation\BookConsultationRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Consultations
 */
class BookingController extends Controller
{
    /** Mes réservations. */
    public function index(Request $request): AnonymousResourceCollection
    {
        return BookingResource::collection(
            $request->user()->bookings()
                ->with(['slot', 'professional.user'])
                ->latest()
                ->paginate(20)
        );
    }

    /** Réserver un créneau. La réservation est confirmée une fois le paiement reçu. */
    public function store(BookConsultationRequest $request, BookConsultation $book): BookingResource
    {
        $booking = $book(
            $request->user(),
            AvailabilitySlot::findOrFail($request->validated('slot_id')),
            ConsultationMode::from($request->validated('mode')),
            $request->boolean('keep_anonymous', true),
            $request->validated('note'),
        );

        return new BookingResource($booking->load(['slot', 'professional.user']));
    }

    public function show(Booking $booking): BookingResource
    {
        $this->authorize('view', $booking);

        return new BookingResource($booking->load(['slot', 'professional.user', 'member']));
    }

    /** Payer la consultation (Mobile Money via MoneyFusion). Renvoie l'URL de paiement. */
    public function pay(PayRequest $request, Booking $booking, InitiatePayment $initiate): PaymentResource
    {
        $this->authorize('pay', $booking);
        abort_unless($booking->status === BookingStatus::PendingPayment, 409, 'Cette réservation n\'est pas en attente de paiement.');

        return new PaymentResource($initiate($request->user(), $booking, $booking->price, $request->provider(), $request->phone()));
    }

    /**
     * Rejoindre la téléconsultation. Lien personnel, valable de 10 minutes avant le rendez-vous
     * à 30 minutes après sa fin.
     */
    public function video(Request $request, Booking $booking, VideoRooms $rooms): JsonResponse
    {
        $this->authorize('join', $booking);

        $access = $rooms->join($booking->load(['slot', 'professional.user', 'member']), $request->user());

        return response()->json(['data' => [
            'url' => $access->url,
            'opens_at' => $access->opensAt->toIso8601String(),
            'closes_at' => $access->closesAt->toIso8601String(),
        ]]);
    }

    public function cancel(Booking $booking, CancelBooking $cancel): BookingResource
    {
        $this->authorize('cancel', $booking);

        return new BookingResource($cancel($booking)->load(['slot', 'professional.user']));
    }
}
