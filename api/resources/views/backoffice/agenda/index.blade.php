<x-admin.page title="Mon agenda" description="Tes créneaux de consultation et les rendez-vous confirmés.">
    @unless ($profile->verified_at)
        <div role="status" class="mb-6 flex items-start gap-3 rounded-lg border border-warning/30 bg-warning-soft px-4 py-3 text-sm">
            <x-icon name="hourglass" :size="18" class="mt-px text-warning" />
            <p>Ton profil est en cours de vérification : il n'apparaît pas encore dans l'annuaire.</p>
        </div>
    @endunless

    <div class="grid gap-4 sm:grid-cols-3">
        <x-admin.stat label="Créneaux à venir" :value="$slots->flatten()->where('is_booked', false)->count()" detail="Disponibles à la réservation" />
        <x-admin.stat label="Consultations à venir" :value="$upcoming->count()" detail="Confirmées et payées" />
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

            <x-admin.card title="Créneaux récurrents" description="Les mêmes horaires, plusieurs semaines d'un coup.">
                <form method="post" action="{{ route('backoffice.agenda.slots.series') }}" class="grid gap-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <x-admin.input name="from" type="date" label="À partir du" :value="today()->addDay()->toDateString()" :min="today()->toDateString()" required />
                        <x-admin.select name="weeks" label="Pendant" :options="collect(range(1, 8))->mapWithKeys(fn ($n) => [$n => trans_choice(':count semaine|:count semaines', $n)])" value="4" />
                    </div>
                    <fieldset>
                        <legend class="mb-2 text-sm font-medium">Jours</legend>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach (['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $index => $day)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="weekdays[]" value="{{ $index + 1 }}" class="peer sr-only" @checked(in_array($index + 1, old('weekdays', [1, 2, 3, 4, 5])))>
                                    <span class="inline-flex h-8 min-w-11 items-center justify-center rounded-md border px-2 text-[13px] font-medium transition-colors peer-checked:border-primary peer-checked:bg-primary peer-checked:text-primary-foreground peer-focus-visible:ring-[3px] peer-focus-visible:ring-ring/40">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('weekdays')<p class="mt-1.5 text-[13px] text-destructive">{{ $message }}</p>@enderror
                    </fieldset>
                    <fieldset>
                        <legend class="mb-2 text-sm font-medium">Heures de début</legend>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach (old('times', ['09:00', '11:00', '15:00']) as $index => $time)
                                <x-admin.input name="times[]" :id="'times-'.$index" type="time" :value="$time" step="900" aria-label="Heure {{ $index + 1 }}" />
                            @endforeach
                        </div>
                        <p class="mt-1.5 text-[13px] text-muted-foreground">Séances de {{ $profile->session_minutes }} min. Les créneaux qui chevauchent un créneau existant sont ignorés.</p>
                    </fieldset>
                    <x-admin.button variant="outline" icon="repeat">Créer la série</x-admin.button>
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

        <div class="grid content-start gap-4 lg:col-span-3">
            <x-admin.card title="Consultations à venir" flush>
                @if ($upcoming->isEmpty())
                    <x-admin.empty icon="calendar-event" title="Aucune consultation à venir" subtitle="Les réservations payées apparaissent ici." />
                @else
                    <ul class="divide-y">
                        @foreach ($upcoming as $booking)
                            @php($start = $booking->slot?->starts_at)
                            <li class="flex flex-wrap items-start gap-4 px-6 py-4">
                                <div class="w-14 shrink-0 rounded-lg border bg-muted/40 py-1.5 text-center">
                                    <p class="text-[11px] font-medium uppercase text-muted-foreground">{{ $start?->translatedFormat('M') }}</p>
                                    <p class="text-lg font-semibold leading-tight tabular-nums">{{ $start?->format('j') }}</p>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium tabular-nums">{{ ucfirst($start?->translatedFormat('l')) }} · {{ $start?->format('H:i') }} – {{ $booking->slot?->ends_at->format('H:i') }}</p>
                                    <p class="mt-0.5 text-sm">
                                        @if ($booking->keep_anonymous)
                                            {{ $booking->member->pseudonym }} <x-admin.badge variant="outline">anonyme</x-admin.badge>
                                        @else
                                            {{ $booking->member->name ?? $booking->member->pseudonym }}
                                            <span class="text-muted-foreground">· {{ $booking->member->phone ?? $booking->member->email }}</span>
                                        @endif
                                    </p>
                                    <p class="mt-1 inline-flex items-center gap-1.5 text-xs text-muted-foreground">
                                        <x-icon :name="match ($booking->mode->value) { 'video' => 'video', 'phone' => 'phone', default => 'map-pin' }" :size="14" />{{ $booking->mode->label() }}
                                    </p>
                                    @if ($booking->member_note)<p class="mt-1.5 max-w-md text-xs italic text-muted-foreground">« {{ $booking->member_note }} »</p>@endif
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    @if ($rooms->applies($booking))
                                        @if ($rooms->isOpen($booking))
                                            <x-admin.button size="sm" icon="video" :href="route('backoffice.agenda.bookings.video', $booking)" target="_blank" rel="noopener">Rejoindre</x-admin.button>
                                        @else
                                            <x-admin.badge variant="outline"><x-icon name="clock" :size="12" />Visio dès {{ $rooms->window($booking)[0]->format('H:i') }}</x-admin.badge>
                                        @endif
                                    @endif
                                    @if ($start?->isPast())
                                        <form method="post" action="{{ route('backoffice.agenda.bookings.complete', $booking) }}">@csrf<x-admin.button variant="outline" size="sm" icon="check">Terminée</x-admin.button></form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>

            <x-admin.card title="Historique" flush>
                @if ($past->isEmpty())
                    <x-admin.empty icon="history" title="Aucune consultation terminée" />
                @else
                    <x-admin.table>
                        <thead><tr><th>Date</th><th>Patient</th><th>Format</th><th class="text-end">Ta part</th></tr></thead>
                        <tbody>
                            @foreach ($past as $booking)
                                <tr>
                                    <td class="whitespace-nowrap tabular-nums">{{ $booking->slot?->starts_at->translatedFormat('D j M, H:i') }}</td>
                                    <td>{{ $booking->keep_anonymous ? $booking->member->pseudonym : ($booking->member->name ?? $booking->member->pseudonym) }}</td>
                                    <td>{{ $booking->mode->label() }}</td>
                                    <td class="text-end tabular-nums">{{ number_format((int) $booking->professional_share, 0, ',', ' ') }} F</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-admin.table>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-admin.page>
