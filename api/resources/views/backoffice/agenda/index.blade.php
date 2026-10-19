<x-backoffice.page title="Mon agenda" pretitle="Espace professionnel">
    <div class="row row-cards">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Ajouter un créneau</h3></div>
                <div class="card-body">
                    <form method="post" action="{{ route('backoffice.agenda.slots.store') }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-6">
                            <label class="form-label" for="date">Date</label>
                            <input type="date" id="date" name="date" class="form-control" min="{{ today()->toDateString() }}" value="{{ old('date', today()->addDay()->toDateString()) }}" required>
                        </div>
                        <div class="col-3">
                            <label class="form-label" for="time">Heure</label>
                            <input type="time" id="time" name="time" class="form-control" value="{{ old('time', '09:00') }}" step="900" required>
                        </div>
                        <div class="col-3"><button class="btn btn-primary w-100" type="submit">Ajouter</button></div>
                    </form>
                    <p class="text-secondary small mt-2 mb-0">Durée d'une séance : {{ $profile->session_minutes }} min · Tarif : <x-backoffice.money :amount="$profile->session_price" /></p>
                    @unless ($profile->verified_at)
                        <div class="alert alert-warning mt-3 mb-0">Ton profil est en cours de vérification : il n'apparaît pas encore dans l'annuaire.</div>
                    @endunless
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Créneaux à venir</h3></div>
                @if ($slots->isEmpty())
                    <div class="card-body"><x-backoffice.empty icon="calendar-plus" title="Aucun créneau à venir" subtitle="Ajoute des créneaux pour recevoir des réservations." /></div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($slots as $day => $daySlots)
                            <div class="list-group-header sticky-top">{{ \Illuminate\Support\Carbon::parse($day)->translatedFormat('l j F') }}</div>
                            @foreach ($daySlots as $slot)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $slot->starts_at->format('H:i') }} – {{ $slot->ends_at->format('H:i') }}</span>
                                    @if ($slot->is_booked)
                                        <span class="badge bg-green-lt">Réservé</span>
                                    @else
                                        <form method="post" action="{{ route('backoffice.agenda.slots.destroy', $slot) }}">
                                            @csrf @method('delete')
                                            <button class="btn btn-sm btn-ghost-danger" type="submit" aria-label="Supprimer le créneau de {{ $slot->starts_at->format('H:i') }}"><i class="ti ti-trash icon"></i></button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Mes consultations</h3></div>
                @if ($bookings->isEmpty())
                    <div class="card-body"><x-backoffice.empty icon="calendar-event" title="Aucune consultation confirmée pour le moment" /></div>
                @else
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead><tr><th>Date</th><th>Patient</th><th>Format</th><th>État</th><th class="w-1"></th></tr></thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->slot?->starts_at->translatedFormat('D j M, H:i') }}</td>
                                        <td>
                                            @if ($booking->keep_anonymous)
                                                {{ $booking->member->pseudonym }} <span class="badge bg-secondary-lt">anonyme</span>
                                            @else
                                                {{ $booking->member->name ?? $booking->member->pseudonym }}
                                                <div class="small text-secondary">{{ $booking->member->phone ?? $booking->member->email }}</div>
                                            @endif
                                            @if ($booking->member_note)<div class="small text-secondary fst-italic">« {{ $booking->member_note }} »</div>@endif
                                        </td>
                                        <td>{{ $booking->mode->label() }}</td>
                                        <td><x-backoffice.status :value="$booking->status" /></td>
                                        <td>
                                            @if ($booking->status->value === 'confirmed')
                                                <form method="post" action="{{ route('backoffice.agenda.bookings.complete', $booking) }}">@csrf<button class="btn btn-sm">Terminée</button></form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-backoffice.page>
