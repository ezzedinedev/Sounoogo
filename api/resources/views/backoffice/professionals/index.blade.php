<x-backoffice.page title="Professionnels" pretitle="Niveau 2">
    <x-slot:actions>
        <a href="{{ route('backoffice.professionals.create') }}" class="btn btn-primary"><i class="ti ti-plus icon"></i>Profil professionnel</a>
    </x-slot:actions>

    <div class="card">
        @if ($professionals->isEmpty())
            <div class="card-body"><x-backoffice.empty icon="stethoscope" title="Aucun professionnel" subtitle="Crée d'abord un compte équipe avec le rôle Professionnel." /></div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Professionnel</th><th>Spécialité</th><th>Tarif</th><th>Créneaux libres</th><th>Réservations</th><th>Annuaire</th><th class="w-1"></th></tr></thead>
                    <tbody>
                        @foreach ($professionals as $professional)
                            <tr>
                                <td>
                                    <div>{{ $professional->user->name }}</div>
                                    <div class="small text-secondary">{{ $professional->title }} · {{ $professional->city }}</div>
                                </td>
                                <td>{{ $professional->specialty }}</td>
                                <td><x-backoffice.money :amount="$professional->session_price" /></td>
                                <td>{{ $professional->slots_count }}</td>
                                <td>{{ $professional->bookings_count }}</td>
                                <td>
                                    @if (! $professional->is_active)
                                        <span class="badge bg-secondary-lt">Inactif</span>
                                    @elseif ($professional->verified_at)
                                        <span class="badge bg-green-lt">Vérifié</span>
                                    @else
                                        <span class="badge bg-yellow-lt">À vérifier</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('backoffice.professionals.edit', $professional) }}" class="btn btn-sm">Modifier</a>
                                        <form method="post" action="{{ route('backoffice.professionals.verification', $professional) }}">
                                            @csrf
                                            <button @class(['btn btn-sm', 'btn-success' => ! $professional->verified_at, 'btn-outline-secondary' => $professional->verified_at])>{{ $professional->verified_at ? 'Retirer' : 'Vérifier' }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $professionals->links() }}</div>
        @endif
    </div>
</x-backoffice.page>
