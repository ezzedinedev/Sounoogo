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
<body class="d-flex flex-column">
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="navbar-brand gap-2">
                <img src="{{ asset('favicon.svg') }}" alt="" width="36" height="36">
                <span class="h2 mb-0">SUNOOGO</span>
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h1 class="h2 text-center mb-2">Espace équipe</h1>
                <p class="text-secondary text-center mb-4">Écoutants, superviseurs, professionnels et administrateurs.</p>
                <form method="post" action="{{ route('backoffice.login.store') }}" autocomplete="off" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="email">Adresse e-mail</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" @class(['form-control', 'is-invalid' => $errors->has('email')]) autocomplete="username" required autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
                    </div>
                    <label class="form-check mb-3">
                        <input type="checkbox" name="remember" value="1" class="form-check-input">
                        <span class="form-check-label">Rester connecté sur cet appareil</span>
                    </label>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
            </div>
        </div>
        <p class="text-center text-secondary mt-3 small">Accès réservé. Toutes les actions sont journalisées.</p>
    </div>
</div>
</body>
</html>
