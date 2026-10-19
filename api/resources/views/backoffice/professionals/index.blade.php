<x-admin.page title="Professionnels" description="Réseau de partenaires indépendants. Seuls les profils vérifiés et actifs apparaissent dans l'annuaire.">
    <x-slot:actions>
        <x-admin.button :href="route('backoffice.professionals.create')" icon="plus">Profil professionnel</x-admin.button>
    </x-slot:actions>

    <x-admin.card flush>
        @if ($professionals->isEmpty())
            <x-admin.empty icon="stethoscope" title="Aucun professionnel" subtitle="Crée d'abord un compte équipe avec le rôle Professionnel." />
        @else
            <x-admin.table>
                <thead><tr><th>Professionnel</th><th>Spécialités</th><th>Tarif</th><th>Créneaux libres</th><th>Réservations</th><th>Annuaire</th><th></th></tr></thead>
                <tbody>
                    @foreach ($professionals as $professional)
                        <tr>
                            <td>
                                <p class="font-medium">{{ $professional->user->name }}</p>
                                <p class="text-xs text-muted-foreground">{{ $professional->title }}@if ($professional->city) · {{ $professional->city }}@endif</p>
                            </td>
                            <td class="max-w-56"><p class="truncate">{{ $professional->specialty }}</p></td>
                            <td><x-admin.money :amount="$professional->session_price" /></td>
                            <td class="tabular-nums">{{ $professional->slots_count }}</td>
                            <td class="tabular-nums">{{ $professional->bookings_count }}</td>
                            <td>
                                @if (! $professional->is_active)
                                    <x-admin.badge>Inactif</x-admin.badge>
                                @elseif ($professional->verified_at)
                                    <x-admin.badge variant="success"><x-icon name="circle-check" :size="14" />Vérifié</x-admin.badge>
                                @else
                                    <x-admin.badge variant="warning">À vérifier</x-admin.badge>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <x-admin.button :href="route('backoffice.professionals.edit', $professional)" variant="outline" size="sm">Modifier</x-admin.button>
                                    <form method="post" action="{{ route('backoffice.professionals.verification', $professional) }}">
                                        @csrf
                                        <x-admin.button :variant="$professional->verified_at ? 'ghost' : 'default'" size="sm">{{ $professional->verified_at ? 'Retirer' : 'Vérifier' }}</x-admin.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
        <x-slot:footer>@if ($professionals->hasPages()){{ $professionals->links() }}@endif</x-slot:footer>
    </x-admin.card>
</x-admin.page>
