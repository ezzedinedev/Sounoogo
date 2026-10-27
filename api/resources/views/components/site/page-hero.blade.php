{{-- Bandeau de page pleine largeur, inspiré du hero architectural MindCorps. --}}
@props([
    'eyebrow' => null,
    'title',
    'accent' => null,
    'contacts' => null,
])
<div {{ $attributes->merge(['class' => 'sunoogo-page-hero']) }}>
    <svg class="sunoogo-page-hero__motif" viewBox="0 0 500 250" fill="none" aria-hidden="true" focusable="false">
        <path d="M125 125C50 125 50 50 125 50S300 200 375 200s75-75 0-75S200 50 125 50" />
        <path d="M125 125c-75 0-75 75 0 75s175-150 250-150 75 75 0 75-175 75-250 75" />
        <circle cx="125" cy="125" r="4" />
        <circle cx="375" cy="125" r="4" />
    </svg>

    <div class="sunoogo-page-hero__copy" data-sunoogo-page-hero-copy>
        @if ($eyebrow)
            <nav class="sunoogo-page-hero__breadcrumb" aria-label="Fil d’Ariane">
                <a href="{{ route('home') }}">Accueil</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $eyebrow }}</span>
            </nav>
        @endif

        <h1 class="sunoogo-page-hero__title">{{ $title }}@if ($accent) <x-site.accent>{{ $accent }}</x-site.accent>@endif</h1>

        @if ($slot->isNotEmpty())
            <p class="sunoogo-page-hero__description">{{ $slot }}</p>
        @endif

        @if ($contacts && count($contacts))
            <div class="sunoogo-page-hero__contact-row" aria-label="Appeler un service d'urgence">
                @foreach ($contacts as $contact)
                    <a href="tel:{{ $contact->phone }}" aria-label="Appeler {{ $contact->label }} au {{ $contact->phone }}">
                        <span>{{ $contact->label }}</span>
                        <strong>{{ $contact->phone }}</strong>
                    </a>
                @endforeach
            </div>
        @else
            <ul class="sunoogo-page-hero__commitments" aria-label="Engagements SUNOOGO">
                <li><span aria-hidden="true"></span>Écoute anonyme</li>
                <li><span aria-hidden="true"></span>Gratuit</li>
                <li><span aria-hidden="true"></span>À ton rythme</li>
            </ul>
        @endif
    </div>

    <div class="sunoogo-page-hero__wave" aria-hidden="true">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M0 48c320 37 720-38 1440 7v25H0z" />
        </svg>
    </div>
</div>
