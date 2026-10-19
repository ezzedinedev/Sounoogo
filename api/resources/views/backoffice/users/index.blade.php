@php use App\Domain\Identity\Enums\Role; @endphp
<x-admin.page title="Utilisateurs" description="Membres et comptes de l'équipe. Les membres n'apparaissent que sous leur pseudonyme.">
    <x-slot:actions>
        <x-admin.button :href="route('backoffice.users.create')" icon="plus">Compte équipe</x-admin.button>
    </x-slot:actions>

    <x-admin.card flush>
        <form method="get" class="flex flex-col gap-2 border-b p-4 sm:flex-row">
            <div class="relative flex-1">
                <x-icon name="search" :size="16" class="pointer-events-none absolute start-3 top-1/2 -translate-y-1/2 text-muted-foreground" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Pseudonyme, nom ou e-mail" aria-label="Rechercher" class="h-9 w-full rounded-md border border-input bg-background ps-9 pe-3 text-sm shadow-xs focus-visible:outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/40">
            </div>
            <x-admin.select name="role" :options="collect(Role::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()])" :value="$role?->value" placeholder="Tous les rôles" class="sm:w-48" aria-label="Rôle" />
            <x-admin.button variant="outline">Filtrer</x-admin.button>
        </form>
        @if ($users->isEmpty())
            <x-admin.empty icon="users" title="Aucun utilisateur trouvé" />
        @else
            <x-admin.table>
                <thead><tr><th>Utilisateur</th><th>Rôle</th><th>Compte</th><th>Inscrit</th><th>Dernière activité</th><th></th></tr></thead>
                <tbody>
                    @foreach ($users as $user)
                        @php($isMember = $user->isOneOf(Role::Member))
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex size-8 items-center justify-center rounded-full bg-muted text-xs font-semibold">{{ mb_substr($isMember ? $user->pseudonym : ($user->name ?? $user->pseudonym), 0, 1) }}</span>
                                    <div>
                                        <p class="font-medium">{{ $isMember ? $user->pseudonym : ($user->name ?? $user->pseudonym) }}</p>
                                        @unless ($isMember)<p class="text-xs text-muted-foreground">{{ $user->email }}</p>@endunless
                                    </div>
                                </div>
                            </td>
                            <td><x-admin.badge variant="outline">{{ $user->primaryRole()?->label() }}</x-admin.badge></td>
                            <td class="text-muted-foreground">{{ $user->is_anonymous ? 'Anonyme' : 'Identifiants' }}</td>
                            <td class="text-muted-foreground whitespace-nowrap">{{ $user->created_at->translatedFormat('j M Y') }}</td>
                            <td class="text-muted-foreground whitespace-nowrap">
                                @if ($user->isSuspended())<x-admin.badge variant="destructive">Suspendu</x-admin.badge>@else{{ $user->last_seen_at?->diffForHumans() ?? '—' }}@endif
                            </td>
                            <td class="text-end">
                                @unless ($user->is(auth()->user()))
                                    <form method="post" action="{{ route('backoffice.users.suspension', $user) }}" onsubmit="return confirm('{{ $user->isSuspended() ? 'Réactiver' : 'Suspendre' }} ce compte ?')">
                                        @csrf
                                        <x-admin.button variant="outline" size="sm" :class="$user->isSuspended() ? '' : 'text-destructive'">{{ $user->isSuspended() ? 'Réactiver' : 'Suspendre' }}</x-admin.button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
        <x-slot:footer>@if ($users->hasPages()){{ $users->links() }}@endif</x-slot:footer>
    </x-admin.card>
</x-admin.page>
