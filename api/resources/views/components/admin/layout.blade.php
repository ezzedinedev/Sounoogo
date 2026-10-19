@props(['title'])
@php
    use App\Domain\Identity\Enums\Role;

    $user = auth()->user();
    $isPro = $user->isOneOf(Role::Professional);

    // [route, libellé, icône, permission (null = rôle pro), motif actif]
    $groups = [
        'Accompagnement' => [
            ['backoffice.dashboard', 'Tableau de bord', 'layout-dashboard', 'backoffice.access', 'backoffice.dashboard'],
            ['backoffice.listening.index', 'Écoute', 'messages', 'conversations.listen', 'backoffice.listening.*'],
            ['backoffice.supervision.index', 'Supervision', 'radar', 'alerts.manage', 'backoffice.supervision.*'],
            ['backoffice.alerts.index', 'Alertes', 'alert-triangle', 'alerts.manage', 'backoffice.alerts.*'],
            ['backoffice.reports.index', 'Signalements', 'flag', 'reports.moderate', 'backoffice.reports.*'],
            ['backoffice.agenda.index', 'Mon agenda', 'calendar', null, 'backoffice.agenda.*'],
        ],
        'Gestion' => [
            ['backoffice.users.index', 'Utilisateurs', 'users', 'users.manage', 'backoffice.users.*'],
            ['backoffice.professionals.index', 'Professionnels', 'stethoscope', 'professionals.manage', 'backoffice.professionals.*'],
            ['backoffice.bookings.index', 'Réservations', 'calendar-event', 'bookings.manage', 'backoffice.bookings.*'],
            ['backoffice.payments.index', 'Paiements', 'receipt', 'payments.view', 'backoffice.payments.*'],
        ],
        'Contenus' => [
            ['backoffice.courses.index', 'Formations', 'school', 'courses.manage', 'backoffice.courses.*'],
            ['backoffice.plans.index', 'Offres', 'tag', 'plans.manage', 'backoffice.plans.*'],
            ['backoffice.emergency-contacts.index', 'Numéros d\'urgence', 'phone-call', 'emergency-contacts.manage', 'backoffice.emergency-contacts.*'],
            ['backoffice.activity.index', 'Journal', 'history', 'activity.view', 'backoffice.activity.*'],
        ],
    ];

    $groups = collect($groups)
        ->map(fn ($items) => collect($items)->filter(fn ($item) => match (true) {
            $item[3] === null => $isPro,
            $isPro && $item[0] === 'backoffice.dashboard' => false,
            default => $user->can($item[3]),
        }))
        ->filter(fn ($items) => $items->isNotEmpty());
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} · SUNOOGO Équipe</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body>
<div class="flex min-h-dvh">
    {{-- Barre latérale --}}
    <aside id="sidebar" class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full transition-transform duration-300 fixed inset-y-0 start-0 z-60 w-64 bg-sidebar border-e border-sidebar-border lg:border-0 lg:translate-x-0 lg:block" role="dialog" tabindex="-1" aria-label="Navigation">
        <div class="flex h-full flex-col">
            <div class="p-3">
                <a href="{{ route($isPro ? 'backoffice.agenda.index' : 'backoffice.dashboard') }}" class="flex items-center gap-2.5 rounded-lg p-2 hover:bg-sidebar-accent">
                    <img src="{{ asset('images/brand/logo-badge.svg') }}" alt="" class="size-8">
                    <span class="leading-tight">
                        <span class="block text-sm font-semibold">SUNOOGO</span>
                        <span class="block text-xs text-muted-foreground">Espace équipe</span>
                    </span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 pb-3" aria-label="Navigation principale">
                @foreach ($groups as $group => $items)
                    <p class="px-2 pt-4 pb-1.5 text-xs font-medium text-muted-foreground">{{ $group }}</p>
                    <ul class="space-y-0.5">
                        @foreach ($items as [$route, $label, $icon, $permission, $pattern])
                            @php($active = request()->routeIs($pattern))
                            <li>
                                <a href="{{ route($route) }}" @class([
                                    'flex h-8 items-center gap-2.5 rounded-md px-2 text-sm transition-colors',
                                    'bg-sidebar-accent font-medium text-sidebar-accent-foreground' => $active,
                                    'text-sidebar-foreground/80 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' => ! $active,
                                ]) @if ($active) aria-current="page" @endif>
                                    <x-icon :name="$icon" :size="16" @class(['text-primary' => $active]) />{{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </nav>

            <div class="border-t border-sidebar-border p-3">
                <div class="hs-dropdown [--placement:top-left] relative w-full">
                    <button id="account-menu" type="button" class="hs-dropdown-toggle flex w-full items-center gap-2.5 rounded-lg p-2 text-start hover:bg-sidebar-accent" aria-haspopup="menu" aria-expanded="false" aria-label="Menu du compte">
                        <span class="inline-flex size-8 items-center justify-center rounded-lg bg-primary-soft text-sm font-semibold text-primary">{{ mb_substr($user->name ?? $user->pseudonym, 0, 1) }}</span>
                        <span class="min-w-0 flex-1 leading-tight">
                            <span class="block truncate text-sm font-medium">{{ $user->name ?? $user->pseudonym }}</span>
                            <span class="block truncate text-xs text-muted-foreground">{{ $user->primaryRole()?->label() }}</span>
                        </span>
                        <x-icon name="selector" :size="16" class="text-muted-foreground" />
                    </button>
                    <div class="hs-dropdown-menu hidden z-70 w-56 rounded-lg border bg-popover p-1 shadow-md opacity-0 transition-[opacity,margin] duration hs-dropdown-open:opacity-100" role="menu" aria-labelledby="account-menu">
                        <div class="px-2 py-1.5 text-xs text-muted-foreground truncate">{{ $user->email }}</div>
                        <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-accent"><x-icon name="external-link" :size="16" />Voir le site</a>
                        <div class="my-1 h-px bg-border"></div>
                        <form method="post" action="{{ route('backoffice.logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-accent"><x-icon name="logout" :size="16" />Se déconnecter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    {{-- Contenu : panneau « inset » arrondi --}}
    <div class="flex min-w-0 flex-1 flex-col lg:ps-64">
        <div class="flex flex-1 flex-col lg:m-2 lg:ms-0 lg:rounded-xl lg:border lg:bg-background lg:shadow-xs bg-background">
            <header class="flex h-14 shrink-0 items-center gap-2 border-b px-4 lg:px-6">
                <button type="button" class="inline-flex size-8 items-center justify-center rounded-md hover:bg-accent lg:hidden" aria-haspopup="dialog" aria-expanded="false" aria-controls="sidebar" aria-label="Ouvrir la navigation" data-hs-overlay="#sidebar">
                    <x-icon name="layout-sidebar" :size="18" />
                </button>
                <span class="mx-1 h-4 w-px bg-border lg:hidden" aria-hidden="true"></span>
                <nav aria-label="Fil d'Ariane" class="flex items-center gap-1.5 text-sm">
                    <a href="{{ route($isPro ? 'backoffice.agenda.index' : 'backoffice.dashboard') }}" class="text-muted-foreground hover:text-foreground">Espace équipe</a>
                    <x-icon name="chevron-right" :size="14" class="text-muted-foreground" />
                    <span class="font-medium" aria-current="page">{{ $title }}</span>
                </nav>

                <div class="ms-auto flex items-center gap-2">
                    @if ($user->can('conversations.listen'))
                        <form method="post" action="{{ route('backoffice.listening.availability') }}">
                            @csrf @method('patch')
                            <button type="submit" @class([
                                'inline-flex h-8 items-center gap-2 rounded-full border px-3 text-[13px] font-medium transition-colors',
                                'border-success/30 bg-success-soft text-success' => $user->is_available,
                                'bg-background text-muted-foreground hover:bg-accent' => ! $user->is_available,
                            ])>
                                <span class="relative flex size-2">
                                    @if ($user->is_available)<span class="absolute inline-flex size-full animate-ping rounded-full bg-success opacity-60"></span>@endif
                                    <span @class(['relative inline-flex size-2 rounded-full', 'bg-success' => $user->is_available, 'bg-muted-foreground/50' => ! $user->is_available])></span>
                                </span>
                                {{ $user->is_available ? 'Disponible' : 'Indisponible' }}
                            </button>
                        </form>
                    @endif
                </div>
            </header>

            <main class="flex-1 px-4 py-6 lg:px-8 lg:py-8">
                {{ $slot }}
            </main>

            <footer class="border-t px-4 py-4 text-xs text-muted-foreground lg:px-8">Les échanges consultés ici sont strictement confidentiels. Toutes les actions sont journalisées.</footer>
        </div>
    </div>
</div>
</body>
</html>
