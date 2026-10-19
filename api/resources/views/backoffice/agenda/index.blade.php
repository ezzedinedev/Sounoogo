<x-admin.page title="Mon agenda" description="Tes créneaux de consultation et les rendez-vous confirmés.">
    @unless ($profile->verified_at)
        <div role="status" class="mb-6 flex items-start gap-3 rounded-lg border border-warning/30 bg-warning-soft px-4 py-3 text-sm">
            <x-icon name="hourglass" :size="18" class="mt-px text-warning" />
            <p>Ton profil est en cours de vérification : il n'apparaît pas encore dans l'annuaire.</p>
        </div>
    @endunless

    <div class="grid gap-4 sm:grid-cols-3">
        <x-admin.stat label="Créneaux à venir" :value="$slots->flatten()->where('is_booked', false)->count()" detail="Disponibles à la réservation" />
        <x-admin.stat label="Consultations confirmées" :value="$bookings->where('status.value', 'confirmed')->count()" detail="À venir ou en cours" />
        <x-admin.stat label="Tarif" :value="number_format($profile->session_price, 0, ',', ' ').' F'" :detail="$profile->session_minutes.' min par séance'" />
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-5">
        <div class="grid content-start gap-4 lg:col-span-2">
            <x-admin.card title="Ajouter un créneau">
                <form method="post" action="{{ route('backoffice.agenda.slots.store') }}" class="grid gap-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.input name="date" type="date" label="Date" :value="today()->addDay()->toDateString()" :min="today()->toDateString()" required />
                        <x-admin.input name="time" type="time" label="Heure" value="09:00" step="900" required />
                    </div>
                    <x-admin.button icon="plus">Ajouter</x-admin.button>
                </form>
            </x-admin.card>

            <x-admin.card title="Créneaux à venir" flush>
                @if ($slots->isEmpty())
                    <x-admin.empty icon="calendar-plus" title="Aucun créneau à venir" subtitle="Ajoute des créneaux pour recevoir des réservations." />
                @else
                    <div class="max-h-[28rem] overflow-y-auto">
                        @foreach ($slots as $day => $daySlots)
                            <p class="sticky top-0 bg-muted/80 px-6 py-2 text-xs font-medium text-muted-foreground backdrop-blur">{{ ucfirst(\Illuminate\Support\Carbon::parse($day)->translatedFormat('l j F')) }}</p>
                            <ul class="divide-y">
                                @foreach ($daySlots as $slot)
                                    <li class="flex items-center justify-between px-6 py-2.5 text-sm">
                                        <span class="tabular-nums">{{ $slot->starts_at->format('H:i') }} – {{ $slot->ends_at->format('H:i') }}</span>
                                        @if ($slot->is_booked)
                                            <x-admin.badge variant="success">Réservé</x-admin.badge>
                                        @else
                                            <form method="post" action="{{ route('backoffice.agenda.slots.destroy', $slot) }}">
                                                @csrf @method('delete')
                                                <x-admin.button variant="ghost" size="icon-sm" aria-label="Supprimer le créneau de {{ $slot->starts_at->format('H:i') }}"><x-icon name="trash" :size="16" class="text-muted-foreground" /></x-admin.button>
                                            </form>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                @endif
            </x-admin.card>
        </div>

        <x-admin.card class="lg:col-span-3" title="Mes consultations" flush>
            @if ($bookings->isEmpty())
                <x-admin.empty icon="calendar-event" title="Aucune consultation confirmée pour le moment" />
            @else
                <x-admin.table>
                    <thead><tr><th>Date</th><th>Patient</th><th>Format</th><th>État</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr>
                                <td class="whitespace-nowrap tabular-nums">{{ $booking->slot?->starts_at->translatedFormat('D j M, H:i') }}</td>
                                <td>
                                    @if ($booking->keep_anonymous)
                                        <span class="font-medium">{{ $booking->member->pseudonym }}</span> <x-admin.badge variant="outline">anonyme</x-admin.badge>
                                    @else
                                        <p class="font-medium">{{ $booking->member->name ?? $booking->member->pseudonym }}</p>
                                        <p class="text-xs text-muted-foreground">{{ $booking->member->phone ?? $booking->member->email }}</p>
                                    @endif
                                    @if ($booking->member_note)<p class="mt-1 max-w-xs text-xs italic text-muted-foreground">« {{ $booking->member_note }} »</p>@endif
                                </td>
                                <td>{{ $booking->mode->label() }}</td>
                                <td><x-admin.status :value="$booking->status" /></td>
                                <td class="text-end">
                                    @if ($booking->status->value === 'confirmed')
                                        <form method="post" action="{{ route('backoffice.agenda.bookings.complete', $booking) }}">@csrf<x-admin.button variant="outline" size="sm">Terminée</x-admin.button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-admin.table>
            @endif
        </x-admin.card>
    </div>
</x-admin.page>
