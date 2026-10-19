<x-admin.page title="Réservations" description="Toutes les consultations réservées par les membres.">
    <x-slot:actions>
        <form method="get">
            <x-admin.select name="statut" :options="collect(\App\Domain\Consultation\Enums\BookingStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])" :value="$status?->value" placeholder="Tous les états" class="w-56" onchange="this.form.submit()" aria-label="Filtrer par état" />
        </form>
    </x-slot:actions>

    <x-admin.card flush>
        @if ($bookings->isEmpty())
            <x-admin.empty icon="calendar-event" title="Aucune réservation" />
        @else
            <x-admin.table>
                <thead><tr><th>Créneau</th><th>Membre</th><th>Professionnel</th><th>Format</th><th class="text-end">Montant</th><th>État</th></tr></thead>
                <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td class="whitespace-nowrap tabular-nums">{{ $booking->slot?->starts_at->translatedFormat('D j M, H:i') ?? '—' }}</td>
                            <td class="font-medium">{{ $booking->member->pseudonym }}</td>
                            <td>{{ $booking->professional->user->name }}</td>
                            <td>{{ $booking->mode->label() }}</td>
                            <td class="text-end"><x-admin.money :amount="$booking->price" /></td>
                            <td><x-admin.status :value="$booking->status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
        <x-slot:footer>@if ($bookings->hasPages()){{ $bookings->links() }}@endif</x-slot:footer>
    </x-admin.card>
</x-admin.page>
