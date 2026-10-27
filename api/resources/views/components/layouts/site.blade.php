@props(['title' => null, 'description' => null, 'noindex' => false, 'heroClass' => 'sunoogo-interior-stage'])
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
    @if (request()->routeIs('home'))
        <script>
            document.documentElement.classList.add('sunoogo-intro-pending');
            window.setTimeout(() => {
                if (document.body?.classList.contains('sunoogo-intro-active')) return;
                document.getElementById('sunoogo-intro')?.remove();
                document.getElementById('sunoogo-site-shell')?.removeAttribute('inert');
                document.documentElement.classList.remove('sunoogo-intro-pending');
            }, 3500);
        </script>
    @endif
    <script>
        try {
            if (sessionStorage.getItem('sunoogo-curtain')) {
                document.documentElement.classList.add('sunoogo-curtain-arriving');
                sessionStorage.removeItem('sunoogo-curtain');
            }
        } catch (_) {}
    </script>
    <link rel="preload" href="{{ asset('fonts/outfit-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/material-symbols-all.woff2') }}" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body class="bg-white text-foreground font-sans antialiased" data-sunoogo-has-hero="{{ isset($hero) && $hero->isNotEmpty() ? 'true' : 'false' }}" @if (request()->routeIs('emergency')) data-sunoogo-motion="off" @endif>
    <div id="sunoogo-site-shell">
        @if (request()->routeIs('home'))
            <script>if (document.documentElement.classList.contains('sunoogo-intro-pending')) document.getElementById('sunoogo-site-shell').setAttribute('inert', '');</script>
        @endif
        <div class="sunoogo-scroll-progress" aria-hidden="true" data-sunoogo-scroll-progress><span></span></div>
        <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:start-3 focus:z-100 focus:bg-white focus:px-4 focus:py-2 focus:rounded-lg">Aller au contenu</a>

        <x-site.emergency-banner :contacts="$emergencyContacts" />

        {{-- Navigation flottante persistante : marque, capsule centrale et action séparées. --}}
        <header class="sunoogo-site-header px-2.5 pt-2.5 sm:px-4 sm:pt-4" data-sunoogo-sticky-header>
            <nav class="sunoogo-nav mx-auto max-w-[1440px]" aria-label="Navigation principale">
                <div class="sunoogo-nav__brand-area">
                    <a href="{{ route('home') }}" class="sunoogo-nav__brand" aria-label="SUNOOGO, accueil">
                        <img src="{{ asset('images/brand/logo-badge.svg') }}" alt="" width="36" height="36">
                        <span>SUNOOGO</span>
                    </a>
                    <button type="button" class="sunoogo-nav__toggle hs-collapse-toggle" id="nav-toggle" aria-expanded="false" aria-controls="nav-menu" aria-label="Menu de navigation" data-hs-collapse="#nav-menu">
                        <x-site.icon name="menu-2" class="sunoogo-nav__toggle-icon sunoogo-nav__toggle-icon--menu" />
                        <x-site.icon name="x" class="sunoogo-nav__toggle-icon sunoogo-nav__toggle-icon--close" />
                    </button>
                </div>

                <div id="nav-menu" class="sunoogo-nav__menu hs-collapse hidden" aria-labelledby="nav-toggle">
                    <div class="sunoogo-nav__links">
                        @foreach ([
                            ['home', 'Accueil'],
                            ['professionals', 'Professionnels'],
                            ['courses', 'Formations'],
                            ['emergency', 'Urgence'],
                        ] as [$route, $label])
                            <a href="{{ route($route) }}" @class([
                                'sunoogo-nav__link',
                                'is-active' => request()->routeIs($route),
                            ]) @if (request()->routeIs($route)) aria-current="page" @endif>{{ $label }}</a>
                        @endforeach
                    </div>
                    <div class="sunoogo-nav__actions">
                        <a href="{{ route('login') }}" class="sunoogo-nav__team">Espace équipe <x-site.icon name="arrow-up-right" :size="15" /></a>
                        <x-site.button :href="route('home').'#application'">Télécharger l'app</x-site.button>
                    </div>
                </div>
            </nav>
        </header>

        @if (isset($hero) && $hero->isNotEmpty())
            <div @class([
                'sunoogo-hero-stage relative isolate overflow-hidden',
                'mx-2.5 mt-2.5 rounded-[1.75rem] sm:mx-4 sm:mt-4 sm:rounded-[2.25rem]' => request()->routeIs('home'),
                'w-full' => ! request()->routeIs('home'),
                $heroClass,
            ]) data-sunoogo-hero>
                {{ $heroBackground ?? '' }}
                @if (! request()->routeIs('home'))
                    <div class="sunoogo-hero-light" aria-hidden="true"></div>
                @endif
                <div class="relative z-10">{{ $hero }}</div>
            </div>
        @endif

        @if (request()->routeIs('home'))
            <div class="sunoogo-marquee" aria-hidden="true">
                <div class="sunoogo-marquee__track">
                    @for ($copy = 0; $copy < 2; $copy++)
                        <span>ANONYME</span><i></i><span>GRATUIT</span><i></i><span>ÉCOUTE HUMAINE</span><i></i><span>À TON RYTHME</span><i></i>
                    @endfor
                </div>
            </div>
        @endif

        <main id="contenu" tabindex="-1">
            {{ $slot }}
        </main>

        <footer class="sunoogo-footer" data-sunoogo-footer>
            <x-site.container class="sunoogo-footer__columns">
                <div class="sunoogo-footer__brand">
                    <a href="{{ route('home') }}" class="sunoogo-footer__logo" aria-label="SUNOOGO, accueil">
                        <span class="sunoogo-footer__logo-mark" aria-hidden="true">
                            <img src="{{ asset('images/brand/logo-badge.svg') }}" alt="" width="30" height="30">
                        </span>
                        <span>SUNOOGO</span>
                    </a>
                    <p>Un espace sûr pour parler de ce qu'on ressent, sans jugement et sans révéler qui on est.</p>
                    <span class="sunoogo-footer__origin"><x-site.icon name="map-pin" :size="15" />Né au Burkina Faso</span>
                    <ul class="sunoogo-footer__platforms" aria-label="Disponibilité de l'application">
                        <li><span><x-site.icon name="brand-android" :size="17" />Android</span><small>Bientôt</small></li>
                        <li><span><x-site.icon name="brand-apple" :size="17" />iPhone</span><small>Bientôt</small></li>
                    </ul>
                </div>

                @foreach ([
                    'Services' => [['Écoute anonyme', route('home').'#niveaux'], ['Consultations', route('professionals')], ['Formations', route('courses')], ['Services gratuits', route('home').'#offres']],
                    'Aide' => [['Numéros d\'urgence', route('emergency')], ['Questions fréquentes', route('home').'#faq'], ['Confidentialité', route('privacy')]],
                    'À propos' => [['Notre approche', route('home').'#approche'], ['Espace équipe', route('login')]],
                ] as $heading => $links)
                    <div class="sunoogo-footer__group">
                        <p>{{ $heading }}</p>
                        <ul>
                            @foreach ($links as [$label, $href])
                                <li><a href="{{ $href }}">{{ $label }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </x-site.container>

            <x-site.container class="sunoogo-footer__legal">
                <p>© {{ now()->year }} SUNOOGO. SUNOOGO ne remplace pas un service d'urgence : en cas de danger, appelle le 17 ou le 18.</p>
                <p><a href="{{ route('privacy') }}">Confidentialité</a><span aria-hidden="true">·</span>Photos Unsplash</p>
            </x-site.container>
        </footer>
    </div>

    <div id="sunoogo-page-curtain" class="sunoogo-page-curtain" aria-hidden="true">
        <div class="sunoogo-page-curtain__brand">
            <img src="{{ asset('images/brand/logo-mark-inverse.svg') }}" alt="" width="58" height="58">
            <span>SUNOOGO</span>
        </div>
    </div>

    @if (request()->routeIs('home'))
        <div id="sunoogo-intro" class="sunoogo-intro" role="dialog" aria-modal="true" aria-labelledby="sunoogo-intro-title">
            <div class="sunoogo-intro__texture" aria-hidden="true"></div>
            <div class="sunoogo-intro__stage">
                <div class="sunoogo-intro__mark-wrap" aria-hidden="true">
                    <span class="sunoogo-intro__orbit"><i></i></span>
                    <span class="sunoogo-intro__glow"></span>
                    <img class="sunoogo-intro__mark" src="{{ asset('images/brand/logo-mark-inverse.svg') }}" alt="" width="112" height="112">
                </div>
                <h1 id="sunoogo-intro-title" class="sunoogo-intro__wordmark" aria-label="SUNOOGO">
                    <span aria-hidden="true"><i>S</i><i>U</i><i>N</i><i>O</i><i>O</i><i>G</i><i>O</i></span>
                </h1>
                <div class="sunoogo-intro__horizon" aria-hidden="true"><span></span></div>
                <p class="sunoogo-intro__tagline"><span>Parle librement.</span><em>Quelqu’un t’écoute.</em></p>
                <p class="sunoogo-intro__promise">ÉCOUTE ANONYME <span></span> GRATUITE</p>
            </div>
            <button type="button" id="sunoogo-intro-skip" class="sunoogo-intro__skip">
                Passer l’intro
                <x-site.icon name="arrow-right" :size="16" />
            </button>
        </div>
    @endif
</body>
</html>
