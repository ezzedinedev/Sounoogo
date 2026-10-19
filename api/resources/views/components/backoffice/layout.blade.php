@props(['title'])
@php
    $user = auth()->user();
    $navigation = [
        ['backoffice.dashboard', 'Tableau de bord', 'layout-dashboard', 'backoffice.access', 'backoffice.dashboard'],
        ['backoffice.listening.index', 'Écoute', 'messages', 'conversations.listen', 'backoffice.listening.*'],
        ['backoffice.alerts.index', 'Alertes', 'alert-triangle', 'alerts.manage', 'backoffice.alerts.*'],
        ['backoffice.reports.index', 'Signalements', 'flag', 'reports.moderate', 'backoffice.reports.*'],
        ['backoffice.agenda.index', 'Mon agenda', 'calendar', null, 'backoffice.agenda.*'],
        ['backoffice.users.index', 'Utilisateurs', 'users', 'users.manage', 'backoffice.users.*'],
        ['backoffice.professionals.index', 'Professionnels', 'stethoscope', 'professionals.manage', 'backoffice.professionals.*'],
        ['backoffice.bookings.index', 'Réservations', 'calendar-event', 'bookings.manage', 'backoffice.bookings.*'],
        ['backoffice.payments.index', 'Paiements', 'receipt', 'payments.view', 'backoffice.payments.*'],
        ['backoffice.courses.index', 'Formations', 'school', 'courses.manage', 'backoffice.courses.*'],
        ['backoffice.plans.index', 'Offres', 'tag', 'plans.manage', 'backoffice.plans.*'],
        ['backoffice.emergency-contacts.index', 'Numéros d\'urgence', 'phone-call', 'emergency-contacts.manage', 'backoffice.emergency-contacts.*'],
        ['backoffice.activity.index', 'Journal', 'history', 'activity.view', 'backoffice.activity.*'],
    ];
    $navigation = array_filter($navigation, fn ($item) => $item[0] === 'backoffice.agenda.index'
        ? $user->isOneOf(\App\Domain\Identity\Enums\Role::Professional)
        : ($item[3] === null || $user->can($item[3])));
    if ($user->isOneOf(\App\Domain\Identity\Enums\Role::Professional)) {
        $navigation = array_filter($navigation, fn ($item) => $item[0] !== 'backoffice.dashboard');
    }
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
<div class="page">
    <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a href="{{ route('backoffice.dashboard') }}" class="navbar-brand navbar-brand-autodark justify-content-start gap-2">
                <img src="{{ asset('images/brand/logo-mark-inverse.svg') }}" alt="" width="28" height="28">
                <span>SUNOOGO</span>
            </a>
            <div class="collapse navbar-collapse" id="sidebar-menu">
                <ul class="navbar-nav pt-lg-3">
                    @foreach ($navigation as [$route, $label, $icon, $permission, $pattern])
                        <li @class(['nav-item', 'active' => request()->routeIs($pattern)])>
                            <a class="nav-link" href="{{ route($route) }}" @if (request()->routeIs($pattern)) aria-current="page" @endif>
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-{{ $icon }} icon"></i></span>
                                <span class="nav-link-title">{{ $label }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </aside>


    <div class="page-wrapper">
        <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
            <div class="container-xl">
                <div class="navbar-nav flex-row order-md-last ms-auto">
                    @if ($user->can('conversations.listen'))
                        <form method="post" action="{{ route('backoffice.listening.availability') }}" class="d-flex align-items-center me-3">
                            @csrf @method('patch')
                            <button type="submit" @class(['btn btn-sm', 'btn-success' => $user->is_available, 'btn-outline-secondary' => ! $user->is_available])>
                                <span @class(['status-dot me-2', 'status-dot-animated status-green' => $user->is_available, 'status-secondary' => ! $user->is_available])></span>
                                {{ $user->is_available ? 'Disponible' : 'Indisponible' }}
                            </button>
                        </form>
                    @endif
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Menu du compte">
                            <span class="avatar avatar-sm">{{ mb_substr($user->name ?? $user->pseudonym, 0, 1) }}</span>
                            <div class="d-none d-xl-block ps-2">
                                <div>{{ $user->name ?? $user->pseudonym }}</div>
                                <div class="mt-1 small text-secondary">{{ $user->primaryRole()?->label() }}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="{{ route('home') }}" class="dropdown-item" target="_blank"><i class="ti ti-external-link me-2"></i>Voir le site</a>
                            <div class="dropdown-divider"></div>
                            <form method="post" action="{{ route('backoffice.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="ti ti-logout me-2"></i>Se déconnecter</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        {{ $slot }}
        <footer class="footer footer-transparent d-print-none">
            <div class="container-xl text-secondary small">SUNOOGO · Espace équipe. Les échanges consultés ici sont strictement confidentiels.</div>
        </footer>
    </div>
</div>
</body>
</html>
