<x-layouts.site>
    {{-- Hero immersif --}}
    <section class="relative isolate overflow-hidden bg-primary-950 text-white">
        <div class="absolute inset-0 -z-10 bg-linear-to-br from-primary-950 via-primary-800 to-primary-600"></div>
        <div class="absolute -top-40 -end-32 -z-10 size-[36rem] rounded-full bg-primary-400/30 blur-3xl"></div>
        <div class="absolute -bottom-48 -start-40 -z-10 size-[32rem] rounded-full bg-lilas-400/25 blur-3xl"></div>

        <x-site.container class="pt-16 pb-24 md:pt-24 md:pb-32 grid gap-16 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-7">
                <p class="inline-flex items-center gap-x-2 rounded-full bg-white/10 px-3.5 py-1.5 text-sm font-medium text-primary-100 ring-1 ring-white/15 backdrop-blur">
                    <x-site.icon name="eye-off" :size="16" />
                    Anonyme · Gratuit · Sans inscription
                </p>
                <h1 class="mt-7 font-display text-5xl sm:text-6xl lg:text-7xl font-semibold tracking-tight leading-[1.02] text-balance">
                    Parle librement.<br>
                    <span class="italic text-lilas-200">Quelqu'un t'écoute.</span>
                </h1>
                <p class="mt-7 max-w-xl text-lg sm:text-xl leading-relaxed text-primary-100 text-pretty">
                    Stress, tristesse, relation qui fait mal, solitude… Des écoutants formés te répondent, sans te juger et sans jamais te demander qui tu es.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-3">
                    <x-site.button href="#application" variant="white" size="xl" icon="arrow-right">Commencer à parler</x-site.button>
                    <x-site.button href="#comment" variant="outline-white" size="xl">Comment ça marche</x-site.button>
                </div>
                <dl class="mt-14 grid grid-cols-1 sm:grid-cols-3 gap-6 border-t border-white/15 pt-8">
                    @foreach ([
                        ['lock', 'Chiffré', 'Tes messages sont chiffrés'],
                        ['user-heart', 'Humain', 'De vrais écoutants formés'],
                        ['shield-check', 'Protégé', 'Un superviseur veille'],
                    ] as [$icon, $title, $text])
                        <div class="flex gap-x-3">
                            <dt class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/15"><x-site.icon :name="$icon" :size="20" /><span class="sr-only">{{ $title }}</span></dt>
                            <dd>
                                <span class="block font-semibold">{{ $title }}</span>
                                <span class="block text-sm text-primary-200">{{ $text }}</span>
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="lg:col-span-5 relative">
                <x-site.phone />
                <div class="hidden sm:flex absolute bottom-40 -start-4 lg:-start-20 items-center gap-x-3 rounded-2xl bg-white px-4 py-3 text-foreground shadow-xl shadow-primary-950/30">
                    <span class="inline-flex size-9 items-center justify-center rounded-full bg-primary-50 text-primary-700"><x-site.icon name="user-heart" :size="18" /></span>
                    <span class="text-sm"><span class="block font-semibold">Un écoutant t'a rejoint</span><span class="text-muted-foreground">formé à l'écoute active</span></span>
                </div>
                <div class="hidden sm:flex absolute -bottom-5 -end-2 lg:-end-8 items-center gap-x-2 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-foreground shadow-xl shadow-primary-950/30">
                    <x-site.icon name="lock" :size="18" class="text-primary-600" />Personne ne sait qui tu es
                </div>
            </div>
        </x-site.container>
    </section>

    {{-- Ce qu'on peut confier --}}
    <section class="py-16 md:py-20 border-b border-line-1">
        <x-site.container>
            <p class="text-center text-sm font-semibold uppercase tracking-wider text-muted-foreground">Pas besoin d'aller très mal pour en parler</p>
            <ul class="mt-8 flex flex-wrap justify-center gap-3">
                @foreach (['Stress au travail', 'Anxiété', 'Tristesse', 'Rupture', 'Dépendance affective', 'Conflits familiaux', 'Deuil', 'Solitude', 'Manque de confiance', 'Pression des examens', 'Violences', 'Insomnie'] as $topic)
                    <li class="rounded-full border border-line-2 bg-white px-4 py-2 text-sm font-medium text-foreground shadow-xs">{{ $topic }}</li>
                @endforeach
            </ul>
        </x-site.container>
    </section>

    {{-- Trois niveaux, en bento --}}
    <section id="niveaux" class="py-24 md:py-32">
        <x-site.container>
            <x-site.section-header eyebrow="Un accompagnement complet" title="Trois façons d'avancer, à ton rythme">
                Commence par parler, gratuitement. Si tu en ressens le besoin, va plus loin avec un professionnel ou un parcours guidé.
            </x-site.section-header>

            <div class="mt-16 grid gap-6 lg:grid-cols-2">
                <article class="relative overflow-hidden rounded-[2rem] bg-primary-50 lg:row-span-2 flex flex-col">
                    <div class="p-8 md:p-10">
                        <div class="flex items-center gap-x-3">
                            <span class="inline-flex size-11 items-center justify-center rounded-2xl bg-primary text-white"><x-site.icon name="message-circle" :size="22" /></span>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-primary-700 ring-1 ring-primary-100">Gratuit, pour toujours</span>
                        </div>
                        <h3 class="mt-6 font-display text-3xl font-semibold">Écoute anonyme</h3>
                        <p class="mt-3 text-lg text-muted-foreground-1 leading-relaxed">Un écoutant formé à l'écoute active te répond par chat. Aucun nom, aucun numéro demandé. Tu peux revenir quand tu veux.</p>
                        <ul class="mt-6 grid gap-3 sm:grid-cols-2 text-sm">
                            @foreach (['Sans inscription identifiante', 'Écoutants formés et supervisés', 'Bouton SOS toujours visible', 'Alerte superviseur si danger'] as $point)
                                <li class="flex gap-x-2"><x-site.icon name="circle-check" :size="18" class="text-primary-600" />{{ $point }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-auto px-8 md:px-10">
                        <img src="{{ asset('images/site/ecoute.webp') }}" alt="Deux amies discutent sur un canapé" loading="lazy" class="w-full aspect-16/10 object-cover rounded-t-3xl">
                    </div>
                </article>

                @foreach ([
                    ['calendar-event', 'Consultation', 'À la séance', 'Psychologues, conseillers et coachs vérifiés. En cabinet, par téléphone ou en visio, en gardant l\'anonymat si tu le souhaites.', route('professionals'), 'Voir les professionnels', 'consultation.webp', 'Une femme se confie à une thérapeute'],
                    ['school', 'Confiance en soi', 'Abonnement', 'Des parcours courts et concrets pour retrouver l\'estime de soi et ton indépendance affective, à ton rythme.', route('courses'), 'Découvrir les formations', 'formations.webp', 'Une personne écrit dans un carnet'],
                ] as [$icon, $title, $badge, $text, $href, $cta, $image, $alt])
                    <article class="group grid sm:grid-cols-5 overflow-hidden rounded-[2rem] border border-card-line bg-card">
                        <div class="sm:col-span-3 p-8 flex flex-col">
                            <div class="flex items-center gap-x-3">
                                <span class="inline-flex size-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-700"><x-site.icon :name="$icon" :size="22" /></span>
                                <span class="rounded-full bg-secondary px-3 py-1 text-xs font-semibold text-secondary-foreground">{{ $badge }}</span>
                            </div>
                            <h3 class="mt-5 font-display text-2xl font-semibold">{{ $title }}</h3>
                            <p class="mt-2 text-muted-foreground-1 leading-relaxed flex-1">{{ $text }}</p>
                            <a href="{{ $href }}" class="mt-5 inline-flex items-center gap-x-1.5 font-semibold text-primary-700 hover:text-primary-900">{{ $cta }}<x-site.icon name="arrow-right" :size="18" class="transition-transform group-hover:translate-x-0.5" /></a>
                        </div>
                        <img src="{{ asset('images/site/'.$image) }}" alt="{{ $alt }}" loading="lazy" class="sm:col-span-2 h-48 sm:h-full w-full object-cover object-top">
                    </article>
                @endforeach
            </div>
        </x-site.container>
    </section>

    {{-- Comment ça marche --}}
    <section id="comment" class="py-24 md:py-32 bg-background-1 border-y border-line-1">
        <x-site.container class="grid gap-16 lg:grid-cols-2 lg:items-center">
            <div>
                <x-site.section-header align="start" eyebrow="Comment ça marche" title="Être écouté·e, en trois minutes" />
                <ol class="mt-12 space-y-10">
                    @foreach ([
                        ['Ouvre l\'application', 'Pas de formulaire : tu reçois un pseudonyme et un code de récupération que toi seul·e connais.'],
                        ['Écris ce que tu ressens', 'Choisis un thème si tu veux, puis écris. Le premier écoutant disponible te rejoint.'],
                        ['Avance à ton rythme', 'Reviens quand tu veux, ou fais-toi accompagner par un professionnel quand tu te sens prêt·e.'],
                    ] as $index => [$title, $text])
                        <li class="relative flex gap-x-6">
                            @unless ($loop->last)<span class="absolute start-6 top-14 -bottom-8 w-px bg-primary-200" aria-hidden="true"></span>@endunless
                            <span class="relative inline-flex size-12 shrink-0 items-center justify-center rounded-full bg-primary font-display text-lg font-semibold text-white ring-8 ring-background-1">{{ $index + 1 }}</span>
                            <div>
                                <h3 class="text-xl font-semibold">{{ $title }}</h3>
                                <p class="mt-2 text-muted-foreground-1 leading-relaxed">{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
            <div class="relative">
                <div class="absolute inset-6 -z-10 rounded-[3rem] bg-linear-to-br from-primary-200 to-lilas-200 blur-2xl opacity-70" aria-hidden="true"></div>
                <img src="{{ asset('images/site/hero.webp') }}" alt="Un jeune homme, les yeux fermés, respire calmement en plein air" loading="lazy" class="w-full aspect-4/5 object-cover rounded-[2.5rem] shadow-2xl shadow-primary-950/20">
            </div>
        </x-site.container>
    </section>

    {{-- Anonymat --}}
    <section class="relative isolate overflow-hidden bg-primary-950 py-24 md:py-32 text-white">
        <div class="absolute top-0 end-0 -z-10 size-[30rem] rounded-full bg-primary-600/30 blur-3xl" aria-hidden="true"></div>
        <x-site.container class="grid gap-14 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-lilas-200">Confidentialité</p>
                <h2 class="mt-4 font-display text-4xl sm:text-5xl font-semibold tracking-tight text-balance">L'anonymat, pour de vrai. Pas seulement en apparence.</h2>
                <p class="mt-6 text-lg leading-relaxed text-primary-200 text-pretty">SUNOOGO est conçu pour qu'il soit techniquement impossible de te reconnaître à partir de ce que tu écris.</p>
                <x-site.button :href="route('privacy')" variant="white" class="mt-10" icon="arrow-right">Notre politique de confidentialité</x-site.button>
            </div>
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    ['eye-off', 'Aucune identité requise', 'Ni nom, ni numéro, ni e-mail pour parler à un écoutant.'],
                    ['lock', 'Messages chiffrés', 'Les échanges sont chiffrés dans notre base de données.'],
                    ['users', 'Pseudonyme uniquement', 'L\'écoutant ne voit qu\'un pseudonyme, jamais ton identité.'],
                    ['shield-check', 'Ni publicité, ni revente', 'Tes données ne sont jamais vendues ni utilisées pour de la publicité.'],
                ] as [$icon, $title, $text])
                    <div class="rounded-3xl bg-white/[0.06] p-6 ring-1 ring-white/10">
                        <dt class="inline-flex size-11 items-center justify-center rounded-2xl bg-white/10 text-lilas-200"><x-site.icon :name="$icon" :size="22" /></dt>
                        <dd class="mt-5">
                            <span class="block text-lg font-semibold">{{ $title }}</span>
                            <span class="mt-1.5 block text-sm leading-relaxed text-primary-200">{{ $text }}</span>
                        </dd>
                    </div>
                @endforeach
            </dl>
        </x-site.container>
    </section>

    {{-- Professionnels --}}
    @if ($professionals->isNotEmpty())
        <section class="py-24 md:py-32">
            <x-site.container>
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                    <x-site.section-header align="start" eyebrow="Consultations" title="Des professionnels vérifiés, près de chez toi">
                        Psychologues, conseillers et coachs certifiés. Tu choisis le format, ils s'adaptent.
                    </x-site.section-header>
                    <x-site.button :href="route('professionals')" variant="secondary" icon="arrow-right">Tous les professionnels</x-site.button>
                </div>
                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    @foreach ($professionals as $professional)
                        @include('site.partials.professional-card', ['professional' => $professional])
                    @endforeach
                </div>
            </x-site.container>
        </section>
    @endif

    {{-- Formations --}}
    @if ($courses->isNotEmpty())
        <section class="py-24 md:py-32 bg-background-1 border-y border-line-1">
            <x-site.container>
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                    <x-site.section-header align="start" eyebrow="Confiance en soi" title="Des parcours pour reprendre confiance">
                        Des leçons courtes et des exercices concrets, conçus avec des professionnels de la santé mentale.
                    </x-site.section-header>
                    <x-site.button :href="route('courses')" variant="secondary" icon="arrow-right">Toutes les formations</x-site.button>
                </div>
                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    @foreach ($courses as $course)
                        @include('site.partials.course-card', ['course' => $course])
                    @endforeach
                </div>
            </x-site.container>
        </section>
    @endif

    {{-- Offres --}}
    <section id="offres" class="py-24 md:py-32">
        <x-site.container>
            <x-site.section-header eyebrow="Offres" title="L'écoute reste toujours gratuite">
                Les abonnements financent les formations et réduisent le prix des consultations. Sans engagement.
            </x-site.section-header>
            <div class="mt-16 grid gap-6 lg:grid-cols-3 lg:items-stretch max-w-5xl mx-auto">
                <div class="flex flex-col rounded-[2rem] border border-card-line bg-card p-8">
                    <h3 class="text-lg font-semibold">Écoute</h3>
                    <p class="mt-1 text-sm text-muted-foreground">Pour tout le monde, tout le temps.</p>
                    <p class="mt-8 font-display text-5xl font-semibold">Gratuit</p>
                    <ul class="mt-8 space-y-3 text-sm flex-1">
                        @foreach (['Écoute anonyme illimitée', 'Bouton SOS et numéros d\'urgence', 'Première formation offerte'] as $feature)
                            <li class="flex gap-x-2"><x-site.icon name="circle-check" :size="18" class="text-primary-600" />{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <x-site.button href="#application" variant="secondary" class="mt-8">Commencer</x-site.button>
                </div>
                @foreach ($plans as $plan)
                    @php($featured = $loop->last)
                    <div @class([
                        'relative flex flex-col rounded-[2rem] p-8',
                        'bg-linear-to-br from-primary-800 to-primary-600 text-white shadow-2xl shadow-primary-700/30 lg:-my-4 lg:py-12' => $featured,
                        'border border-card-line bg-card' => ! $featured,
                    ])>
                        @if ($featured)
                            <span class="absolute -top-3.5 start-8 rounded-full bg-white px-3 py-1 text-xs font-semibold text-primary-700 shadow">Le plus complet</span>
                        @endif
                        <h3 class="text-lg font-semibold">{{ $plan->name }}</h3>
                        <p @class(['mt-1 text-sm', 'text-primary-100' => $featured, 'text-muted-foreground' => ! $featured])>{{ $plan->description }}</p>
                        <p class="mt-8 font-display text-5xl font-semibold">
                            {{ number_format($plan->price, 0, ',', ' ') }}<span class="text-2xl"> F</span>
                            <span @class(['text-base font-sans font-normal', 'text-primary-100' => $featured, 'text-muted-foreground' => ! $featured])>/ mois</span>
                        </p>
                        <ul class="mt-8 space-y-3 text-sm flex-1">
                            @foreach ($plan->features ?? [] as $feature)
                                <li class="flex gap-x-2"><x-site.icon name="circle-check" :size="18" @class(['text-lilas-200' => $featured, 'text-primary-600' => ! $featured]) />{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <x-site.button href="#application" :variant="$featured ? 'white' : 'primary'" class="mt-8">Choisir {{ $plan->name }}</x-site.button>
                    </div>
                @endforeach
            </div>
            <p class="mt-10 text-center text-sm text-muted-foreground">Paiement par Wave ou Orange Money directement dans l'application. Sans carte bancaire.</p>
        </x-site.container>
    </section>

    {{-- Urgence --}}
    <section class="px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto rounded-[2rem] bg-sos-soft ring-1 ring-sos-line px-6 py-10 sm:px-12 grid gap-8 md:grid-cols-5 md:items-center">
            <div class="md:col-span-3">
                <p class="inline-flex items-center gap-x-2 font-semibold text-sos-strong"><x-site.icon name="lifebuoy" />Si tu es en danger maintenant</p>
                <p class="mt-3 text-lg leading-relaxed text-sos-strong/90">SUNOOGO ne remplace pas les secours. Si ta vie ou celle de quelqu'un est menacée, appelle immédiatement l'un de ces numéros. Dans l'application, le bouton SOS prévient aussi un superviseur.</p>
            </div>
            <ul class="md:col-span-2 grid gap-3">
                @foreach ($emergencyContacts as $contact)
                    <li>
                        <a href="tel:{{ $contact->phone }}" class="flex items-center justify-between rounded-2xl bg-white px-5 py-3.5 ring-1 ring-sos-line hover:ring-sos">
                            <span class="font-medium text-foreground">{{ $contact->label }}</span>
                            <span class="inline-flex items-center gap-x-2 font-display text-2xl font-semibold text-sos"><x-site.icon name="phone-call" :size="18" />{{ $contact->phone }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-24 md:py-32">
        <x-site.container class="grid gap-12 lg:grid-cols-3">
            <x-site.section-header align="start" eyebrow="Questions fréquentes" title="Tu te demandes peut-être…">
                Une autre question ? Pose-la directement à un écoutant dans l'application.
            </x-site.section-header>
            <div class="lg:col-span-2 hs-accordion-group divide-y divide-line-2 border-y border-line-2">
                @foreach ($faq as $index => [$question, $answer])
                    <div @class(['hs-accordion', 'active' => $index === 0]) id="faq-{{ $index }}">
                        <button type="button" class="hs-accordion-toggle w-full py-6 inline-flex items-center justify-between gap-x-4 text-start text-lg font-semibold text-foreground hover:text-primary-700" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faq-{{ $index }}-content">
                            {{ $question }}
                            <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary-700"><x-site.icon name="chevron-down" :size="18" class="transition-transform hs-accordion-active:rotate-180" /></span>
                        </button>
                        <div id="faq-{{ $index }}-content" @class(['hs-accordion-content w-full overflow-hidden transition-[height] duration-300', 'hidden' => $index !== 0]) role="region" aria-labelledby="faq-{{ $index }}">
                            <p class="pb-6 pe-12 text-muted-foreground-1 leading-relaxed">{{ $answer }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-site.container>
    </section>

    {{-- Appel final --}}
    <section id="application" class="px-4 sm:px-6 lg:px-8 pb-24 md:pb-32">
        <div class="relative isolate max-w-6xl mx-auto overflow-hidden rounded-[2.5rem] bg-linear-to-br from-primary-900 via-primary-700 to-primary-500 px-6 pt-14 sm:px-14 md:pt-20 grid lg:grid-cols-2 gap-12 text-white">
            <div class="absolute -top-24 -start-24 -z-10 size-96 rounded-full bg-lilas-400/30 blur-3xl" aria-hidden="true"></div>
            <div class="pb-14 md:pb-20">
                <h2 class="font-display text-4xl sm:text-5xl font-semibold tracking-tight text-balance">Quelqu'un t'attend pour t'écouter.</h2>
                <p class="mt-5 text-lg text-primary-100 leading-relaxed">L'application SUNOOGO arrive sur Android et iPhone. Nous ouvrons le service en priorité au Burkina Faso.</p>
                <div class="mt-10 flex flex-col sm:flex-row gap-3">
                    <x-site.button variant="white" size="xl" disabled aria-disabled="true"><x-site.icon name="brand-android" :size="20" />Android · bientôt</x-site.button>
                    <x-site.button variant="outline-white" size="xl" disabled aria-disabled="true"><x-site.icon name="brand-apple" :size="20" />iPhone · bientôt</x-site.button>
                </div>
            </div>
            <div class="self-end">
                <x-site.phone class="translate-y-6" />
            </div>
        </div>
    </section>
</x-layouts.site>
