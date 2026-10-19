<x-backoffice.page title="Réservations" pretitle="Niveau 2">
    <x-slot:actions>
        <form method="get">
            <select name="statut" class="form-select" onchange="this.form.submit()" aria-label="Filtrer par état">
                <option value="">Tous les états</option>
                @foreach (\App\Domain\Consultation\Enums\BookingStatus::cases() as $case)
                    <option value="{{ $case->value }}" @selected($status === $case)>{{ $case->label() }}</option>
                @endforeach
            </select>
        </form>
    </x-slot:actions>

    <div class="card">
        @if ($bookings->isEmpty())
            <div class="card-body"><x-backoffice.empty icon="calendar-event" title="Aucune réservation" /></div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Créneau</th><th>Membre</th><th>Professionnel</th><th>Format</th><th>Montant</th><th>État</th></tr></thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr>
                                <td>{{ $booking->slot?->starts_at->translatedFormat('D j M, H:i') ?? '—' }}</td>
                                <td>{{ $booking->member->pseudonym }}</td>
                                <td>{{ $booking->professional->user->name }}</td>
                                <td>{{ $booking->mode->label() }}</td>
                                <td><x-backoffice.money :amount="$booking->price" /></td>
                                <td><x-backoffice.status :value="$booking->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $bookings->links() }}</div>
        @endif
    </div>
</x-backoffice.page>
