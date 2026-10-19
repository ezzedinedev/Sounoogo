@props(["title" => null, "description" => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="theme-sunoogo">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "{$title} · SUNOOGO" : 'SUNOOGO — Parle librement, quelqu\'un t\'écoute' }}</title>
    <meta name="description" content="{{ $description ?? 'Écoute anonyme et gratuite, consultations avec des professionnels et parcours de confiance en soi. Au Burkina Faso.' }}">
    <meta name="theme-color" content="#602fb8">
    <meta property="og:title" content="{{ $title ?? 'SUNOOGO' }}">
    <meta property="og:description" content="{{ $description ?? 'Écoute anonyme et gratuite, consultations et formations pour la santé mentale.' }}">
    <meta property="og:image" content="{{ asset('images/brand/og-default.png') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @fonts
    @vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body class="bg-background text-foreground font-sans antialiased">
    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:start-3 focus:z-100 focus:bg-layer focus:px-4 focus:py-2 focus:rounded-lg">Aller au contenu</a>

    <x-site.emergency-banner :contacts="$emergencyContacts" />

    <header class="sticky top-0 inset-x-0 z-50 bg-navbar backdrop-blur-md border-b border-navbar-line">
        <nav class="max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-3 md:flex md:items-center md:justify-between md:gap-x-6" aria-label="Navigation principale">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-x-2 font-display text-xl font-semibold text-foreground" aria-label="SUNOOGO, accueil">
                    <img src="{{ asset('images/brand/logo-badge.svg') }}" alt="" class="size-9">
                    SUNOOGO
                </a>
                <button type="button" class="hs-collapse-toggle md:hidden inline-flex items-center justify-center size-10 rounded-full text-foreground hover:bg-navbar-nav-hover" id="nav-toggle" aria-expanded="false" aria-controls="nav-menu" aria-label="Ouvrir le menu" data-hs-collapse="#nav-menu">
                    <x-site.icon name="menu-2" class="hs-collapse-open:hidden" />
                    <x-site.icon name="x" class="hidden hs-collapse-open:block" />
                </button>
            </div>

            <div id="nav-menu" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow md:block" aria-labelledby="nav-toggle">
                <div class="flex flex-col gap-1 pt-4 md:pt-0 md:flex-row md:items-center md:justify-end md:gap-2">
                    @foreach ([
                        ['home', 'Accueil'],
                        ['professionals', 'Professionnels'],
                        ['courses', 'Formations'],
                        ['emergency', 'Urgence'],
                    ] as [$route, $label])
                        <a href="{{ route($route) }}" @class([
                            'px-3 py-2 rounded-full text-sm font-medium text-navbar-nav-foreground hover:bg-navbar-nav-hover',
                            'bg-navbar-nav-active' => request()->routeIs($route),
                        ]) @if (request()->routeIs($route)) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                    <x-site.button :href="route('home').'#application'" class="mt-2 md:mt-0 md:ms-2">Télécharger l'app</x-site.button>
                </div>
            </div>
        </nav>
    </header>

    <main id="contenu">
        {{ $slot }}
    </main>

    <footer class="bg-footer text-white/80">
        <x-site.container class="py-14 grid gap-10 md:grid-cols-4">
            <div class="md:col-span-2 max-w-sm">
                <p class="inline-flex items-center gap-x-3 font-display text-2xl font-semibold text-white">
                    <img src="{{ asset('images/brand/logo-mark-inverse.svg') }}" alt="" class="size-9">
                    SUNOOGO
                </p>
                <p class="mt-3 text-sm leading-relaxed">Un espace sûr pour parler de ce qu'on ressent, sans jugement et sans révéler qui on est. Né au Burkina Faso.</p>
            </div>
            <div>
                <p class="text-sm font-semibold text-white">Services</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a class="hover:text-white" href="{{ route('home') }}#niveaux">Écoute anonyme</a></li>
                    <li><a class="hover:text-white" href="{{ route('professionals') }}">Consultations</a></li>
                    <li><a class="hover:text-white" href="{{ route('courses') }}">Formations</a></li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-semibold text-white">SUNOOGO</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a class="hover:text-white" href="{{ route('privacy') }}">Confidentialité</a></li>
                    <li><a class="hover:text-white" href="{{ route('emergency') }}">Numéros d'urgence</a></li>
                    <li><a class="hover:text-white" href="{{ route('login') }}">Espace équipe</a></li>
                </ul>
            </div>
        </x-site.container>
        <div class="border-t border-footer-line">
            <x-site.container class="py-6 text-xs text-white/60 flex flex-col sm:flex-row gap-2 justify-between">
                <p>© {{ now()->year }} SUNOOGO. SUNOOGO ne remplace pas un service d'urgence.</p>
                <p>Photos : Unsplash</p>
            </x-site.container>
        </div>
    </footer>
</body>
</html>
