@props(['title' => null, 'description' => null, 'noindex' => false, 'heroClass' => 'bg-linear-to-b from-primary-100 via-primary-50 to-white'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="theme-sunoogo">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "{$title} · SUNOOGO" : 'SUNOOGO — Parle librement, quelqu\'un t\'écoute' }}</title>
    <meta name="description" content="{{ $description ?? 'Écoute anonyme et gratuite, consultations avec des professionnels et parcours de confiance en soi. Au Burkina Faso.' }}">
    <meta name="theme-color" content="#602fb8">
    @if ($noindex)<meta name="robots" content="noindex, nofollow">@endif
    <meta property="og:title" content="{{ $title ?? 'SUNOOGO' }}">
    <meta property="og:description" content="{{ $description ?? 'Écoute anonyme et gratuite, consultations et formations pour la santé mentale.' }}">
    <meta property="og:image" content="{{ asset('images/brand/og-default.png') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @fonts
    @vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body class="bg-white text-foreground font-sans antialiased">
    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:start-3 focus:z-100 focus:bg-white focus:px-4 focus:py-2 focus:rounded-lg">Aller au contenu</a>

    <x-site.emergency-banner :contacts="$emergencyContacts" />

    {{-- Bloc d'ouverture arrondi, navigation flottante à l'intérieur (structure inspirée de Lyra Health). --}}
    <div class="p-2.5 sm:p-4">
        <div class="relative isolate overflow-hidden rounded-[1.75rem] sm:rounded-[2.25rem] {{ $heroClass }}">
            {{ $heroBackground ?? '' }}

            <header class="relative z-20 px-3 pt-3 sm:px-6 sm:pt-6">
                <nav class="mx-auto max-w-6xl rounded-2xl bg-white/75 backdrop-blur-md ring-1 ring-white/60 shadow-sm px-4 py-3 md:flex md:items-center md:justify-between md:gap-x-6" aria-label="Navigation principale">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-x-2.5 text-lg font-semibold tracking-tight text-primary-950" aria-label="SUNOOGO, accueil">
                            <img src="{{ asset('images/brand/logo-badge.svg') }}" alt="" class="size-9">
                            SUNOOGO
                        </a>
                        <button type="button" class="hs-collapse-toggle md:hidden inline-flex items-center justify-center size-10 rounded-full text-foreground hover:bg-primary-50" id="nav-toggle" aria-expanded="false" aria-controls="nav-menu" aria-label="Ouvrir le menu" data-hs-collapse="#nav-menu">
                            <x-icon name="menu-2" class="hs-collapse-open:hidden" />
                            <x-icon name="x" class="hidden hs-collapse-open:block" />
                        </button>
                    </div>

                    <div id="nav-menu" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow md:block" aria-labelledby="nav-toggle">
                        <div class="flex flex-col gap-1 pt-4 md:pt-0 md:flex-row md:items-center md:justify-between md:gap-2">
                            <div class="flex flex-col md:flex-row md:items-center md:gap-1 md:ms-6">
                                @foreach ([
                                    ['home', 'Accueil'],
                                    ['professionals', 'Professionnels'],
                                    ['courses', 'Formations'],
                                    ['emergency', 'Urgence'],
                                ] as [$route, $label])
                                    <a href="{{ route($route) }}" @class([
                                        'px-3.5 py-2 rounded-full text-[15px] font-medium text-primary-950/80 hover:text-primary-950 hover:bg-primary-50',
                                        'text-primary-950! bg-primary-50' => request()->routeIs($route),
                                    ]) @if (request()->routeIs($route)) aria-current="page" @endif>{{ $label }}</a>
                                @endforeach
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center gap-2 mt-2 md:mt-0">
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-x-2 px-3.5 py-2 text-[15px] font-medium text-primary-950/80 hover:text-primary-950">
                                    Espace équipe <span class="inline-flex size-7 items-center justify-center rounded-full bg-white ring-1 ring-primary-100"><x-icon name="arrow-up-right" :size="14" /></span>
                                </a>
                                <x-site.button :href="route('home').'#application'">Télécharger l'app</x-site.button>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>

            {{ $hero ?? '' }}
        </div>
    </div>

    <main id="contenu">
        {{ $slot }}
    </main>

    <footer class="border-t border-line-2">
        <x-site.container class="py-16 grid gap-12 md:grid-cols-12">
            <div class="md:col-span-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-x-2.5 text-lg font-semibold text-primary-950">
                    <img src="{{ asset('images/brand/logo-badge.svg') }}" alt="" class="size-9">SUNOOGO
                </a>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-muted-foreground-1">Un espace sûr pour parler de ce qu'on ressent, sans jugement et sans révéler qui on est. Né au Burkina Faso.</p>
            </div>
            @foreach ([
                'Services' => [['Écoute anonyme', route('home').'#niveaux'], ['Consultations', route('professionals')], ['Formations', route('courses')], ['Offres', route('home').'#offres']],
                'Aide' => [['Numéros d\'urgence', route('emergency')], ['Questions fréquentes', route('home').'#faq'], ['Confidentialité', route('privacy')]],
                'SUNOOGO' => [['Espace équipe', route('login')], ['Notre approche', route('home').'#approche']],
            ] as $heading => $links)
                <div class="md:col-span-2">
                    <p class="text-sm text-muted-foreground">{{ $heading }}</p>
                    <ul class="mt-4 space-y-3 text-[15px]">
                        @foreach ($links as [$label, $href])
                            <li><a class="text-foreground hover:text-primary-700" href="{{ $href }}">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
            <div class="md:col-span-2">
                <p class="text-sm text-muted-foreground">Application</p>
                <ul class="mt-4 space-y-3 text-[15px] text-muted-foreground-1">
                    <li class="inline-flex items-center gap-x-2"><x-icon name="brand-android" :size="18" />Android · bientôt</li>
                    <li class="inline-flex items-center gap-x-2"><x-icon name="brand-apple" :size="18" />iPhone · bientôt</li>
                </ul>
            </div>
        </x-site.container>
        <x-site.container class="py-6 border-t border-line-2 flex flex-col sm:flex-row gap-3 justify-between text-xs text-muted-foreground">
            <p>© {{ now()->year }} SUNOOGO. SUNOOGO ne remplace pas un service d'urgence : en cas de danger, appelle le 17 ou le 18.</p>
            <p><a href="{{ route('privacy') }}" class="hover:text-foreground">Confidentialité</a> · Photos Unsplash</p>
        </x-site.container>
    </footer>
</body>
</html>
