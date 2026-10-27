<x-layouts.site hero-class="bg-[#602fb8]">
    {{-- Photo de fond du hero : mur violet, la personne à droite, le texte à gauche. --}}
    <x-slot:heroBackground>
        <img src="{{ asset('images/site/hero-lyra.webp') }}" alt="" class="absolute inset-0 -z-10 size-full object-cover object-[70%_30%]" fetchpriority="high">
        <div class="absolute inset-0 -z-10 bg-linear-to-r from-[#24123f]/85 via-[#382265]/55 to-transparent" data-home-hero-shade></div>
        <div class="absolute inset-x-0 bottom-0 -z-10 h-40 bg-linear-to-t from-[#24123f]/50 to-transparent"></div>
    </x-slot:heroBackground>

    <x-slot:hero>
        <div class="relative mx-auto max-w-6xl px-6 sm:px-10 pt-40 pb-16 sm:pt-48 sm:pb-24 lg:pt-56 lg:pb-32 text-white">
            <div class="max-w-xl">
                <h1 class="text-5xl sm:text-6xl lg:text-[4.25rem] font-medium tracking-tight leading-[1.05] text-balance" data-home-hero-title>
                    Parle librement.
                    <x-site.accent class="block mt-1 text-lilas-200">quelqu'un t'écoute.</x-site.accent>
                </h1>
                <p class="mt-7 max-w-md text-lg leading-relaxed text-white/85">
                    Une écoute anonyme et gratuite par des écoutants formés, puis des professionnels et des parcours pour aller mieux, à ton rythme.
                </p>
                <div class="mt-10 flex flex-wrap items-center gap-x-6 gap-y-4">
                    <x-site.button href="#application" variant="white" size="lg">Commencer à parler</x-site.button>
                    <x-site.arrow-link href="#approche" tone="inverse">Comment ça marche</x-site.arrow-link>
                </div>
            </div>

            <a href="{{ route('emergency') }}" class="hidden md:flex absolute bottom-10 end-10 max-w-xs items-center gap-x-4 rounded-2xl bg-white/15 p-4 pe-5 backdrop-blur-md ring-1 ring-white/25 hover:bg-white/20">
                <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-white text-sos"><x-site.icon name="lifebuoy" :size="24" /></span>
                <span class="text-sm leading-snug">
                    <span class="block font-semibold">En danger maintenant ?</span>
                    <span class="text-white/80">Police 17 · Pompiers 18. Le bouton SOS de l'app prévient un superviseur.</span>
                </span>
            </a>
        </div>
    </x-slot:hero>

    {{-- Engagements (à la place des logos clients : uniquement des faits vérifiables) --}}
    <section class="py-20 md:py-24">
        <x-site.container>
            <h2 class="text-center text-3xl sm:text-4xl font-medium tracking-tight">Une écoute pensée <x-site.accent>pour toi, ici</x-site.accent></h2>
            <ul class="mt-14 grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-10">
                @foreach ([
                    ['eye-off', 'Anonyme', 'Aucun nom, aucun numéro'],
                    ['heart-handshake', 'Gratuit', 'L\'écoute ne coûte rien'],
                    ['user-heart', 'Humain', 'Des écoutants formés'],
                    ['shield-check', 'Supervisé', 'Un superviseur veille'],
                ] as [$icon, $title, $text])
                    <li class="flex flex-col items-center text-center">
                        <span class="inline-flex size-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-700"><x-site.icon :name="$icon" :size="26" /></span>
                        <span class="mt-4 text-lg font-medium">{{ $title }}</span>
                        <span class="mt-1 text-sm text-muted-foreground">{{ $text }}</span>
                    </li>
                @endforeach
            </ul>
        </x-site.container>
    </section>

    {{-- Trois niveaux, en onglets, avec des écrans de l'application --}}
    <section id="niveaux" class="py-20 md:py-28">
        <x-site.container>
            <x-site.section-header title="Trois façons d'avancer," accent="à ton rythme">
                SUNOOGO t'accompagne du premier message jusqu'à un suivi professionnel.
            </x-site.section-header>

            <nav class="mt-12 flex flex-wrap justify-center gap-2" aria-label="Niveaux d'accompagnement" role="tablist" aria-orientation="horizontal">
                @foreach (['Écoute anonyme', 'Consultation', 'Confiance en soi'] as $index => $label)
                    <button type="button" @class([
                        'hs-tab-active:bg-white hs-tab-active:ring-primary-950 hs-tab-active:text-primary-950 min-w-36 rounded-xl px-5 py-2.5 text-[15px] font-medium text-muted-foreground-1 bg-background-2 ring-1 ring-transparent hover:text-primary-950',
                        'active' => $index === 0,
                    ]) id="niveau-tab-{{ $index }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}" data-hs-tab="#niveau-{{ $index }}" aria-controls="niveau-{{ $index }}" role="tab">{{ $label }}</button>
                @endforeach
            </nav>

            <div class="mt-14">
                @foreach ([
                    ['Gratuit', 'Un écoutant formé à l\'écoute active te répond par chat. Aucun nom, aucun numéro demandé. Si tes messages montrent que tu es en danger, un superviseur est alerté immédiatement.', 'site.partials.screens-listening'],
                    ['Gratuit', 'Psychologues, conseillers et coachs vérifiés par notre équipe. En cabinet, par téléphone ou en visio, en gardant l\'anonymat si tu le souhaites.', 'site.partials.screens-consultation'],
                    ['Gratuit', 'Des parcours courts et concrets pour retrouver l\'estime de soi et ton indépendance affective. Une leçon par jour, quelques minutes, des exercices simples.', 'site.partials.screens-learning'],
                ] as $index => [$badge, $text, $screens])
                    <div id="niveau-{{ $index }}" role="tabpanel" aria-labelledby="niveau-tab-{{ $index }}" @class(['hidden' => $index !== 0])>
                        <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
                            <div class="lg:col-span-4">
                                <span class="inline-flex rounded-full bg-primary-50 px-3 py-1 text-sm font-medium text-primary-800">{{ $badge }}</span>
                                <p class="mt-5 text-lg leading-relaxed text-muted-foreground-1">{{ $text }}</p>
                                <x-site.arrow-link :href="$index === 1 ? route('professionals') : ($index === 2 ? route('courses') : '#application')" class="mt-8">En savoir plus</x-site.arrow-link>
                            </div>
                            <div class="lg:col-span-8">
                                @include($screens)
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-site.container>
    </section>

    {{-- Pour qui --}}
    <section id="approche" class="py-20 md:py-28">
        <x-site.container>
            <x-site.section-header title="Un accompagnement" accent="pour chacun" />
            <div class="mt-16 grid gap-12 lg:grid-cols-2 lg:items-center">
                <img src="{{ asset('images/site/membre.webp') }}" alt="Une jeune femme en foulard sourit, la main sur la joue" loading="lazy" class="w-full aspect-4/5 object-cover object-top rounded-[2rem]">
                <div class="hs-accordion-group divide-y divide-line-3 border-b border-line-3">
                    @foreach ([
                        ['Pour toi', 'Ouvre l\'application et écris. Pas de formulaire : tu reçois un pseudonyme et un code de récupération que toi seul·e connais. Le premier écoutant disponible te rejoint.'],
                        ['Pour un proche', 'Tu t\'inquiètes pour quelqu\'un ? Parle-en à un écoutant : il t\'aidera à trouver les mots et les bons relais, sans que ton proche soit identifié.'],
                        ['Pour les professionnels de santé', 'Psychologues, conseillers, coachs : rejoins le réseau de partenaires indépendants, partage tes disponibilités et accompagne des personnes qui ont fait le premier pas.'],
                        ['Pour les associations et ONG', 'Orientez vos bénéficiaires vers une écoute anonyme et gratuite, encadrée par des superviseurs cliniques, avec un protocole d\'urgence intégré.'],
                    ] as $index => [$title, $text])
                        <div @class(['hs-accordion', 'active' => $index === 0]) id="pour-{{ $index }}">
                            <button type="button" class="hs-accordion-toggle w-full py-6 inline-flex items-center justify-between gap-x-4 text-start text-2xl font-medium text-foreground" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="pour-{{ $index }}-content">
                                <span class="inline-flex items-center gap-x-3">
                                    <span class="size-2 rounded-full bg-primary-500 opacity-0 hs-accordion-active:opacity-100 transition-opacity"></span>
                                    {{ $title }}
                                </span>
                                <x-site.icon name="arrow-right" :size="22" class="transition-transform hs-accordion-active:rotate-90" />
                            </button>
                            <div id="pour-{{ $index }}-content" @class(['hs-accordion-content w-full overflow-hidden transition-[height] duration-300', 'hidden' => $index !== 0]) role="region" aria-labelledby="pour-{{ $index }}">
                                <p class="pb-7 ps-5 text-[17px] leading-relaxed text-muted-foreground-1">{{ $text }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-site.container>
    </section>

    {{-- Engagements chiffrés : uniquement des faits vrais --}}
    <section class="py-20 md:py-28">
        <x-site.container>
            <x-site.section-header title="Nos engagements," accent="noir sur blanc" />
            <div class="mt-16 grid gap-6 md:grid-cols-3">
                @foreach ([
                    ['Gratuit', 'pour accéder aux services SUNOOGO', 'from-primary-100'],
                    ['0', 'donnée personnelle demandée pour être écouté·e', 'from-lilas-100'],
                    ['3', 'niveaux d\'accompagnement, de l\'écoute au suivi professionnel', 'from-primary-100'],
                ] as [$figure, $text, $from])
                    <div class="rounded-[2rem] bg-linear-to-b {{ $from }} to-white ring-1 ring-primary-100/70 px-8 pt-14 pb-10 text-center">
                        <p class="font-display italic text-7xl text-primary-950">{{ $figure }}</p>
                        <p class="mt-5 text-lg leading-snug text-foreground">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-12 flex justify-center">
                <x-site.arrow-link :href="route('privacy')">Lire notre politique de confidentialité</x-site.arrow-link>
            </div>
        </x-site.container>
    </section>

    {{-- Professionnels --}}
    @if ($professionals->isNotEmpty())
        <section class="py-20 md:py-28 bg-background-1">
            <x-site.container>
                <x-site.section-header title="Des professionnels vérifiés," accent="près de chez toi">
                    Tu choisis le format, ils s'adaptent. Tu peux rester anonyme jusqu'à la séance.
                </x-site.section-header>
                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    @foreach ($professionals as $professional)
                        @include('site.partials.professional-card', ['professional' => $professional])
                    @endforeach
                </div>
                <div class="mt-12 flex justify-center">
                    <x-site.arrow-link :href="route('professionals')">Voir tous les professionnels</x-site.arrow-link>
                </div>
            </x-site.container>
        </section>
    @endif

    {{-- Formations --}}
    @if ($courses->isNotEmpty())
        <section class="py-20 md:py-28">
            <x-site.container>
                <x-site.section-header title="Des parcours pour" accent="reprendre confiance" />
                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    @foreach ($courses as $course)
                        @include('site.partials.course-card', ['course' => $course])
                    @endforeach
                </div>
                <div class="mt-12 flex justify-center">
                    <x-site.arrow-link :href="route('courses')">Toutes les formations</x-site.arrow-link>
                </div>
            </x-site.container>
        </section>
    @endif

    {{-- Services gratuits --}}
    <section id="offres" class="py-20 md:py-28">
        <x-site.container>
            <x-site.section-header title="Tous nos services sont" accent="gratuits">
                Écoute, consultations et parcours : tout est gratuit.
            </x-site.section-header>
            <div class="mt-16 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ([
                    ['eye-off', 'Écoute anonyme', 'Un espace de parole confidentiel avec des écoutants formés.', ['Sans nom ni numéro', 'Un superviseur veille'], route('home').'#application'],
                    ['heart-handshake', 'Consultations', 'Des professionnels vérifiés, en cabinet, au téléphone ou en visio.', ['Psychologues et conseillers', 'Choix du format'], route('professionals')],
                    ['book', 'Parcours', 'Des exercices guidés pour avancer à ton rythme.', ['Confiance en soi', 'Leçons courtes'], route('courses')],
                ] as [$icon, $name, $description, $features, $href])
                    <article @class(['sunoogo-free-card', 'sunoogo-free-card--featured' => $loop->last])>
                        <div class="sunoogo-free-card__top">
                            <span class="sunoogo-free-card__icon"><x-site.icon :name="$icon" :size="23" /></span>
                            <span class="sunoogo-free-card__badge">Gratuit</span>
                        </div>
                        <h3>{{ $name }}</h3>
                        <p class="sunoogo-free-card__description">{{ $description }}</p>
                        <ul>
                            @foreach ($features as $feature)
                                <li><x-site.icon name="check" :size="17" />{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <x-site.button :href="$href" :variant="$loop->last ? 'white' : 'primary'" class="sunoogo-free-card__action">Découvrir</x-site.button>
                    </article>
                @endforeach
            </div>
        </x-site.container>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="py-20 md:py-28">
        <x-site.container class="max-w-5xl">
            <h2 class="text-center text-4xl sm:text-5xl font-medium tracking-tight">Questions fréquentes</h2>
            <div class="mt-14 rounded-[2rem] bg-primary-50 px-6 sm:px-12 py-6 hs-accordion-group">
                @foreach ($faq as $index => [$question, $answer])
                    <div class="hs-accordion border-b border-primary-200 last:border-b-0" id="faq-{{ $index }}">
                        <button type="button" class="hs-accordion-toggle w-full py-7 inline-flex items-center justify-between gap-x-6 text-start text-xl sm:text-2xl font-medium text-foreground" aria-expanded="false" aria-controls="faq-{{ $index }}-content">
                            {{ $question }}
                            <x-site.icon name="x" :size="22" class="shrink-0 rotate-45 transition-transform hs-accordion-active:rotate-0" />
                        </button>
                        <div id="faq-{{ $index }}-content" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="faq-{{ $index }}">
                            <p class="pb-7 pe-12 text-lg leading-relaxed text-muted-foreground-1">{{ $answer }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-site.container>
    </section>

    {{-- Appel final --}}
    <section id="application" class="py-24 md:py-32 text-center">
        <x-site.container class="max-w-3xl">
            <h2 class="text-4xl sm:text-5xl font-medium tracking-tight text-balance">Une écoute pensée pour <x-site.accent>la vraie vie</x-site.accent></h2>
            <p class="mt-5 text-lg text-muted-foreground-1">L'application SUNOOGO arrive sur Android et iPhone. Nous ouvrons le service en priorité au Burkina Faso.</p>
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
                <x-site.button size="lg" disabled aria-disabled="true"><x-site.icon name="brand-android" :size="20" />Android · bientôt</x-site.button>
                <x-site.button variant="secondary" size="lg" disabled aria-disabled="true"><x-site.icon name="brand-apple" :size="20" />iPhone · bientôt</x-site.button>
            </div>
        </x-site.container>
    </section>
</x-layouts.site>
