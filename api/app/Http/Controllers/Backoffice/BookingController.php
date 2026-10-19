<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = BookingStatus::tryFrom((string) $request->query('statut'));

        return view('backoffice.bookings.index', [
            'bookings' => Booking::with(['member', 'professional.user', 'slot'])
                ->when($status, fn ($q) => $q->where('status', $status))
                ->latest()
                ->paginate(25)
                ->withQueryString(),
            'status' => $status,
        ]);
    }
}
