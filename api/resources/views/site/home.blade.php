<x-layouts.site>
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <x-site.container class="pt-12 pb-16 md:pt-20 md:pb-24 grid gap-12 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-6">
                <p class="inline-flex items-center gap-x-2 rounded-full bg-accent-soft px-3 py-1 text-sm font-medium text-accent-strong">
                    <x-site.icon name="eye-off" :size="16" />
                    Anonyme, gratuit, sans inscription
                </p>
                <h1 class="mt-6 font-display text-4xl sm:text-5xl lg:text-6xl font-semibold tracking-tight text-balance leading-[1.05]">
                    Parle librement.
                    <span class="block italic text-accent">Quelqu'un t'écoute.</span>
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-muted-foreground-1 text-pretty">
                    Stress, tristesse, relation qui fait mal, solitude… Sur SUNOOGO, des écoutants formés te répondent sans te juger et sans jamais te demander qui tu es.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <x-site.button href="#application" size="lg" icon="arrow-right">Commencer à parler</x-site.button>
                    <x-site.button href="#comment" variant="secondary" size="lg">Comment ça marche</x-site.button>
                </div>
                <ul class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    @foreach ([
                        ['lock', 'Messages chiffrés'],
                        ['user-heart', 'Écoutants formés'],
                        ['shield-check', 'Superviseur en cas d\'urgence'],
                    ] as [$icon, $label])
                        <li class="flex items-center gap-x-2 text-muted-foreground-1">
                            <span class="inline-flex size-8 items-center justify-center rounded-full bg-primary-50 text-primary-700"><x-site.icon :name="$icon" :size="16" /></span>
                            {{ $label }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-6 relative">
                <div class="relative aspect-4/5 sm:aspect-5/4 lg:aspect-4/5 overflow-hidden rounded-[2rem] bg-surface">
                    <img src="{{ asset('images/site/hero.webp') }}" alt="Un jeune homme, les yeux fermés, respire calmement en plein air" class="size-full object-cover" fetchpriority="high">
                </div>

                {{-- Aperçu d'une conversation : montre la promesse plutôt que de la décrire. --}}
                <figure class="absolute -bottom-6 start-4 end-4 sm:start-auto sm:-start-8 sm:end-auto sm:w-80 rounded-2xl bg-layer/95 backdrop-blur border border-layer-line p-4 shadow-xl shadow-primary-950/10">
                    <figcaption class="flex items-center gap-x-2 text-xs text-muted-foreground">
                        <span class="size-2 rounded-full bg-online" aria-hidden="true"></span>
                        Mariam, écoutante · en ligne
                    </figcaption>
                    <p class="mt-3 rounded-2xl rounded-ss-sm bg-primary-50 px-3.5 py-2.5 text-sm text-primary-950">Bonsoir, je suis là. Prends ton temps, tu peux tout me dire.</p>
                    <p class="mt-2 ms-auto w-fit rounded-2xl rounded-se-sm bg-primary px-3.5 py-2.5 text-sm text-primary-foreground">Merci… ça fait du bien d'en parler.</p>
                </figure>
            </div>
        </x-site.container>
    </section>

    {{-- Trois niveaux --}}
    <section id="niveaux" class="py-20 md:py-28 bg-background-1 border-y border-line-1">
        <x-site.container>
            <x-site.section-header eyebrow="Nos services" title="Trois façons d'avancer, à ton rythme">
                Commence par parler, gratuitement. Si tu en ressens le besoin, va plus loin avec un professionnel ou un parcours guidé.
            </x-site.section-header>

            <div class="mt-14 grid gap-6 lg:grid-cols-3">
                @foreach ([
                    ['01', 'Écoute anonyme', 'Gratuit', 'ecoute.webp', 'Deux amies discutent sur un canapé', 'Un écoutant formé à l\'écoute active te répond par chat. Aucun nom, aucun numéro demandé.', ['Sans inscription identifiante', 'Chat aujourd\'hui, appel bientôt', 'Alerte superviseur en cas de danger']],
                    ['02', 'Consultation', 'À la séance', 'consultation.webp', 'Une femme parle à une thérapeute', 'Psychologues, conseillers et coachs vérifiés, en cabinet, par téléphone ou en visio.', ['Professionnels vérifiés', 'Anonymat maintenu si tu le souhaites', 'Paiement Wave ou Orange Money']],
                    ['03', 'Confiance en soi', 'Abonnement', 'formations.webp', 'Une personne écrit dans un carnet', 'Des parcours courts et concrets pour retrouver l\'estime de soi et ton indépendance affective.', ['Leçons de quelques minutes', 'Exercices pratiques', 'Nouveaux contenus chaque mois']],
                ] as [$number, $title, $badge, $image, $alt, $text, $points])
                    <article class="group flex flex-col rounded-3xl bg-card border border-card-line overflow-hidden">
                        <div class="aspect-16/10 overflow-hidden bg-surface">
                            <img src="{{ asset('images/site/'.$image) }}" alt="{{ $alt }}" loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03] motion-reduce:transition-none">
                        </div>
                        <div class="flex flex-1 flex-col p-6 md:p-7">
                            <div class="flex items-center justify-between">
                                <span class="font-display text-sm italic text-accent">{{ $number }}</span>
                                <span class="rounded-full bg-secondary px-2.5 py-1 text-xs font-medium text-secondary-foreground">{{ $badge }}</span>
                            </div>
                            <h3 class="mt-3 text-xl font-semibold">{{ $title }}</h3>
                            <p class="mt-2 text-muted-foreground-1 leading-relaxed">{{ $text }}</p>
                            <ul class="mt-5 space-y-2 text-sm">
                                @foreach ($points as $point)
                                    <li class="flex gap-x-2"><x-site.icon name="check" :size="18" class="text-accent mt-px" />{{ $point }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </article>
                @endforeach
            </div>
        </x-site.container>
    </section>

    {{-- Comment ça marche --}}
    <section id="comment" class="py-20 md:py-28">
        <x-site.container>
            <x-site.section-header eyebrow="Comment ça marche" title="Trois minutes pour être écouté·e" />
            <ol class="mt-14 grid gap-10 md:grid-cols-3">
                @foreach ([
                    ['device-mobile', 'Ouvre l\'application', 'Pas de formulaire : tu reçois un pseudonyme et un code de récupération que toi seul·e connais.'],
                    ['message-circle', 'Écris ce que tu ressens', 'Choisis un thème si tu veux, puis écris. Le premier écoutant disponible te rejoint.'],
                    ['heart-handshake', 'Avance à ton rythme', 'Reviens quand tu veux, ou fais-toi accompagner par un professionnel quand tu te sens prêt·e.'],
                ] as $index => [$icon, $title, $text])
                    <li class="relative">
                        <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-primary text-primary-foreground"><x-site.icon :name="$icon" :size="22" /></span>
                        <p class="mt-5 text-sm font-medium text-muted-foreground">Étape {{ $index + 1 }}</p>
                        <h3 class="mt-1 text-lg font-semibold">{{ $title }}</h3>
                        <p class="mt-2 text-muted-foreground-1 leading-relaxed">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </x-site.container>
    </section>

    {{-- Anonymat --}}
    <section class="px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto rounded-[2rem] bg-primary text-primary-foreground px-6 py-14 sm:px-12 md:py-20 grid gap-12 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-on-primary-accent">Confidentialité</p>
                <h2 class="mt-3 font-display text-3xl sm:text-4xl font-semibold tracking-tight text-balance">L'anonymat, pour de vrai. Pas seulement en apparence.</h2>
                <p class="mt-4 text-lg leading-relaxed text-primary-100 text-pretty">Nous avons conçu SUNOOGO pour qu'il soit techniquement impossible de te reconnaître à partir de ce que tu écris.</p>
                <x-site.button :href="route('privacy')" variant="secondary" class="mt-8" icon="arrow-right">Notre politique de confidentialité</x-site.button>
            </div>
            <dl class="grid gap-6 sm:grid-cols-2">
                @foreach ([
                    ['eye-off', 'Aucune identité requise', 'Ni nom, ni numéro, ni e-mail pour parler à un écoutant.'],
                    ['lock', 'Messages chiffrés', 'Les échanges sont chiffrés dans notre base de données.'],
                    ['users', 'Pseudonyme uniquement', 'L\'écoutant ne voit qu\'un pseudonyme, jamais ton identité.'],
                    ['shield-check', 'Ni publicité, ni revente', 'Tes données ne sont jamais vendues ni utilisées pour de la publicité.'],
                ] as [$icon, $title, $text])
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                        <dt class="flex items-center gap-x-2 font-semibold"><x-site.icon :name="$icon" :size="18" class="text-on-primary-accent" />{{ $title }}</dt>
                        <dd class="mt-2 text-sm leading-relaxed text-primary-100">{{ $text }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- Professionnels --}}
    @if ($professionals->isNotEmpty())
        <section class="py-20 md:py-28">
            <x-site.container>
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                    <x-site.section-header align="start" eyebrow="Niveau 2" title="Des professionnels vérifiés, près de chez toi">
                        Psychologues, conseillers et coachs certifiés. Tu choisis le format, ils s'adaptent.
                    </x-site.section-header>
                    <x-site.button :href="route('professionals')" variant="ghost" icon="arrow-right">Voir tous les professionnels</x-site.button>
                </div>
                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    @foreach ($professionals as $professional)
                        @include('site.partials.professional-card', ['professional' => $professional])
                    @endforeach
                </div>
            </x-site.container>
        </section>
    @endif

    {{-- Formations --}}
    @if ($courses->isNotEmpty())
        <section class="py-20 md:py-28 bg-background-1 border-y border-line-1">
            <x-site.container>
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                    <x-site.section-header align="start" eyebrow="Niveau 3" title="Des parcours pour reprendre confiance">
                        Des leçons courtes et des exercices concrets, conçus avec des professionnels de la santé mentale.
                    </x-site.section-header>
                    <x-site.button :href="route('courses')" variant="ghost" icon="arrow-right">Toutes les formations</x-site.button>
                </div>
                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    @foreach ($courses as $course)
                        @include('site.partials.course-card', ['course' => $course])
                    @endforeach
                </div>
            </x-site.container>
        </section>
    @endif

    {{-- Offres --}}
    <section id="offres" class="py-20 md:py-28">
        <x-site.container>
            <x-site.section-header eyebrow="Offres" title="L'écoute reste toujours gratuite">
                Les abonnements financent les formations et réduisent le prix des consultations. Sans engagement.
            </x-site.section-header>
            <div class="mt-14 grid gap-6 lg:grid-cols-3 max-w-5xl mx-auto">
                <div class="flex flex-col rounded-3xl border border-card-line bg-card p-7">
                    <h3 class="text-lg font-semibold">Écoute</h3>
                    <p class="mt-1 text-sm text-muted-foreground">Pour tout le monde, tout le temps.</p>
                    <p class="mt-6 font-display text-4xl font-semibold">Gratuit</p>
                    <ul class="mt-6 space-y-3 text-sm flex-1">
                        @foreach (['Écoute anonyme illimitée', 'Bouton SOS et numéros d\'urgence', 'Première formation offerte'] as $feature)
                            <li class="flex gap-x-2"><x-site.icon name="check" :size="18" class="text-accent" />{{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
                @foreach ($plans as $plan)
                    <div @class([
                        'relative flex flex-col rounded-3xl p-7 border',
                        'bg-primary text-primary-foreground border-primary' => $loop->last,
                        'bg-card border-card-line' => ! $loop->last,
                    ])>
                        @if ($loop->last)
                            <span class="absolute -top-3 start-7 rounded-full bg-white px-3 py-1 text-xs font-semibold text-primary-700 ring-1 ring-primary-200">Le plus complet</span>
                        @endif
                        <h3 class="text-lg font-semibold">{{ $plan->name }}</h3>
                        <p @class(['mt-1 text-sm', 'text-primary-100' => $loop->last, 'text-muted-foreground' => ! $loop->last])>{{ $plan->description }}</p>
                        <p class="mt-6 font-display text-4xl font-semibold">
                            <x-site.price :amount="$plan->price" />
                            <span @class(['text-base font-sans font-normal', 'text-primary-100' => $loop->last, 'text-muted-foreground' => ! $loop->last])>/ mois</span>
                        </p>
                        <ul class="mt-6 space-y-3 text-sm flex-1">
                            @foreach ($plan->features ?? [] as $feature)
                                <li class="flex gap-x-2"><x-site.icon name="check" :size="18" @class(['text-on-primary-accent' => $loop->parent->last, 'text-accent' => ! $loop->parent->last]) />{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
            <p class="mt-8 text-center text-sm text-muted-foreground">Paiement par Wave ou Orange Money directement dans l'application.</p>
        </x-site.container>
    </section>

    {{-- Urgence --}}
    <section class="px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto rounded-[2rem] bg-sos-soft border border-sos-line px-6 py-10 sm:px-12 grid gap-8 md:grid-cols-5 md:items-center">
            <div class="md:col-span-3">
                <p class="inline-flex items-center gap-x-2 font-semibold text-sos-strong"><x-site.icon name="lifebuoy" />Si tu es en danger maintenant</p>
                <p class="mt-3 text-lg leading-relaxed text-sos-strong/90">SUNOOGO ne remplace pas les secours. Si ta vie ou celle de quelqu'un est menacée, appelle immédiatement l'un de ces numéros. Dans l'application, le bouton SOS prévient aussi un superviseur.</p>
            </div>
            <ul class="md:col-span-2 grid gap-3">
                @foreach ($emergencyContacts as $contact)
                    <li>
                        <a href="tel:{{ $contact->phone }}" class="flex items-center justify-between rounded-2xl bg-white px-5 py-3.5 border border-sos-line hover:border-sos">
                            <span class="font-medium text-foreground">{{ $contact->label }}</span>
                            <span class="inline-flex items-center gap-x-2 font-display text-2xl font-semibold text-sos"><x-site.icon name="phone-call" :size="18" />{{ $contact->phone }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-20 md:py-28">
        <x-site.container class="max-w-3xl">
            <x-site.section-header eyebrow="Questions fréquentes" title="Tu te demandes peut-être…" />
            <div class="mt-12 hs-accordion-group divide-y divide-line-2 border-y border-line-2">
                @foreach ($faq as $index => [$question, $answer])
                    <div @class(['hs-accordion', 'active' => $index === 0]) id="faq-{{ $index }}">
                        <button type="button" class="hs-accordion-toggle w-full py-5 inline-flex items-center justify-between gap-x-4 text-start text-lg font-semibold text-foreground hover:text-primary-700" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faq-{{ $index }}-content">
                            {{ $question }}
                            <x-site.icon name="chevron-down" class="transition-transform hs-accordion-active:rotate-180 text-muted-foreground" />
                        </button>
                        <div id="faq-{{ $index }}-content" @class(['hs-accordion-content w-full overflow-hidden transition-[height] duration-300', 'hidden' => $index !== 0]) role="region" aria-labelledby="faq-{{ $index }}">
                            <p class="pb-6 text-muted-foreground-1 leading-relaxed">{{ $answer }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-site.container>
    </section>

    {{-- Application --}}
    <section id="application" class="pb-20 md:pb-28 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto overflow-hidden rounded-[2rem] bg-card border border-card-line grid lg:grid-cols-2">
            <div class="p-8 sm:p-12 md:p-14 flex flex-col justify-center">
                <h2 class="font-display text-3xl sm:text-4xl font-semibold tracking-tight text-balance">Quelqu'un t'attend pour t'écouter.</h2>
                <p class="mt-4 text-lg text-muted-foreground-1 leading-relaxed">L'application SUNOOGO arrive sur Android et iPhone. Laisse-nous ton envie d'en parler : nous ouvrons le service en priorité au Burkina Faso.</p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <x-site.button variant="primary" size="lg" disabled aria-disabled="true">
                        <x-site.icon name="brand-android" :size="20" /> Android · bientôt
                    </x-site.button>
                    <x-site.button variant="secondary" size="lg" disabled aria-disabled="true">
                        <x-site.icon name="brand-apple" :size="20" /> iPhone · bientôt
                    </x-site.button>
                </div>
            </div>
            <div class="min-h-72 bg-surface">
                <img src="{{ asset('images/site/communaute.webp') }}" alt="Trois amies rient ensemble en plein air" loading="lazy" class="size-full object-cover">
            </div>
        </div>
    </section>
</x-layouts.site>
