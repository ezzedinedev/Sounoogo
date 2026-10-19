<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = PaymentStatus::tryFrom((string) $request->query('statut'));

        return view('backoffice.payments.index', [
            'payments' => Payment::with('user')
                ->when($status, fn ($q) => $q->where('status', $status))
                ->when($request->query('q'), fn ($q, $ref) => $q->where('reference', 'like', "%{$ref}%"))
                ->latest()
                ->paginate(30)
                ->withQueryString(),
            'status' => $status,
            'totalMonth' => (int) Payment::where('status', PaymentStatus::Succeeded)->where('paid_at', '>=', now()->startOfMonth())->sum('amount'),
        ]);
    }
}
