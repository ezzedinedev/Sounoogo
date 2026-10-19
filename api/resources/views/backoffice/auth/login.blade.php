<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion · SUNOOGO Équipe</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="bg-background">
<div class="grid min-h-dvh lg:grid-cols-2">
    <div class="flex flex-col p-6 md:p-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-semibold">
            <img src="{{ asset('images/brand/logo-badge.svg') }}" alt="" class="size-8">SUNOOGO
        </a>
        <div class="flex flex-1 items-center justify-center">
            <div class="w-full max-w-sm">
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-semibold tracking-tight">Espace équipe</h1>
                    <p class="mt-2 text-sm text-muted-foreground">Écoutants, superviseurs, professionnels et administrateurs.</p>
                </div>
                <form method="post" action="{{ route('backoffice.login.store') }}" class="grid gap-5" novalidate>
                    @csrf
                    <x-admin.input name="email" type="email" label="Adresse e-mail" autocomplete="username" required autofocus placeholder="prenom@exemple.org" />
                    <x-admin.input name="password" type="password" label="Mot de passe" autocomplete="current-password" required />
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-input text-primary focus:ring-ring/40">
                        Rester connecté sur cet appareil
                    </label>
                    <x-admin.button class="w-full">Se connecter</x-admin.button>
                </form>
                <p class="mt-8 text-center text-xs text-muted-foreground">Accès réservé. Toutes les actions sont journalisées.</p>
            </div>
        </div>
    </div>
    <div class="relative hidden overflow-hidden bg-primary lg:block">
        <img src="{{ asset('images/site/hero-lyra.webp') }}" alt="" class="absolute inset-0 size-full object-cover object-[70%_center] opacity-90">
        <div class="absolute inset-0 bg-linear-to-t from-black/50 via-transparent to-transparent"></div>
        <blockquote class="absolute inset-x-10 bottom-10 text-white">
            <p class="text-2xl font-medium leading-snug">« Écouter sans juger, c'est déjà aider. »</p>
            <footer class="mt-3 text-sm text-white/75">Principe d'écoute SUNOOGO</footer>
        </blockquote>
    </div>
</div>
</body>
</html>
