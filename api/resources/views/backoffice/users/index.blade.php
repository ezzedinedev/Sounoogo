<x-backoffice.page title="Utilisateurs" pretitle="Administration">
    <x-slot:actions>
        <a href="{{ route('backoffice.users.create') }}" class="btn btn-primary"><i class="ti ti-plus icon"></i>Compte équipe</a>
    </x-slot:actions>

    <div class="card">
        <div class="card-body border-bottom py-3">
            <form method="get" class="row g-2">
                <div class="col-md-5"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Pseudonyme, nom ou e-mail" aria-label="Rechercher"></div>
                <div class="col-md-4">
                    <select name="role" class="form-select" aria-label="Rôle">
                        <option value="">Tous les rôles</option>
                        @foreach (\App\Domain\Identity\Enums\Role::cases() as $case)
                            <option value="{{ $case->value }}" @selected($role === $case)>{{ $case->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><button class="btn w-100" type="submit">Filtrer</button></div>
            </form>
        </div>
        @if ($users->isEmpty())
            <div class="card-body"><x-backoffice.empty icon="users" title="Aucun utilisateur trouvé" /></div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Utilisateur</th><th>Rôle</th><th>Type de compte</th><th>Inscrit</th><th>Dernière activité</th><th class="w-1"></th></tr></thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-sm">{{ mb_substr($user->name ?? $user->pseudonym, 0, 1) }}</span>
                                        <div>
                                            <div>{{ $user->isOneOf(\App\Domain\Identity\Enums\Role::Member) ? $user->pseudonym : ($user->name ?? $user->pseudonym) }}</div>
                                            @unless ($user->isOneOf(\App\Domain\Identity\Enums\Role::Member))<div class="small text-secondary">{{ $user->email }}</div>@endunless
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->primaryRole()?->label() }}</td>
                                <td>{{ $user->is_anonymous ? 'Anonyme' : 'Identifiants' }}</td>
                                <td class="text-secondary">{{ $user->created_at->translatedFormat('j M Y') }}</td>
                                <td class="text-secondary">{{ $user->last_seen_at?->diffForHumans() ?? '—' }}</td>
                                <td>
                                    @if (! $user->is(auth()->user()))
                                        <form method="post" action="{{ route('backoffice.users.suspension', $user) }}" onsubmit="return confirm('{{ $user->isSuspended() ? 'Réactiver' : 'Suspendre' }} ce compte ?')">
                                            @csrf
                                            <button @class(['btn btn-sm', 'btn-outline-danger' => ! $user->isSuspended(), 'btn-outline-success' => $user->isSuspended()])>{{ $user->isSuspended() ? 'Réactiver' : 'Suspendre' }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $users->links() }}</div>
        @endif
    </div>
</x-backoffice.page>
