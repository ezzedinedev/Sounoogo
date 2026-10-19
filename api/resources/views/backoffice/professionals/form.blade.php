@php($editing = $professional->exists)
<x-backoffice.page :title="$editing ? $professional->user->name : 'Nouveau profil professionnel'" pretitle="Professionnels">
    <form method="post" action="{{ $editing ? route('backoffice.professionals.update', $professional) : route('backoffice.professionals.store') }}" class="card">
        @csrf
        @if ($editing) @method('put') @endif
        <div class="card-body">
            <div class="row">
                @unless ($editing)
                    <div class="col-12 mb-3">
                        <label class="form-label required" for="user_id">Compte</label>
                        @if ($candidates->isEmpty())
                            <div class="alert alert-info mb-0">Aucun compte professionnel sans profil. <a href="{{ route('backoffice.users.create') }}">Crée d'abord le compte</a> avec le rôle Professionnel.</div>
                        @else
                            <select id="user_id" name="user_id" class="form-select" required>
                                @foreach ($candidates as $candidate)
                                    <option value="{{ $candidate->id }}" @selected(old('user_id', $professional->user_id) == $candidate->id)>{{ $candidate->name }} · {{ $candidate->email }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                @endunless
                <div class="col-md-6 mb-3">
                    <label class="form-label required" for="title">Titre</label>
                    <input id="title" name="title" value="{{ old('title', $professional->title) }}" class="form-control" placeholder="Psychologue clinicienne" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label required" for="specialty">Spécialités</label>
                    <input id="specialty" name="specialty" value="{{ old('specialty', $professional->specialty) }}" class="form-control" placeholder="Anxiété, couple, deuil…" required>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label" for="bio">Présentation</label>
                    <textarea id="bio" name="bio" rows="4" class="form-control">{{ old('bio', $professional->bio) }}</textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="city">Ville</label>
                    <input id="city" name="city" value="{{ old('city', $professional->city) }}" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="license_number">N° d'agrément / ordre</label>
                    <input id="license_number" name="license_number" value="{{ old('license_number', $professional->license_number) }}" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="languages">Langues</label>
                    <input id="languages" name="languages" value="{{ old('languages', implode(', ', $professional->languages ?? [])) }}" class="form-control" placeholder="Français, Mooré, Dioula">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required" for="session_price">Tarif (F CFA)</label>
                    <input type="number" id="session_price" name="session_price" min="0" step="500" value="{{ old('session_price', $professional->session_price) }}" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required" for="session_minutes">Durée (min)</label>
                    <input type="number" id="session_minutes" name="session_minutes" min="15" max="180" value="{{ old('session_minutes', $professional->session_minutes) }}" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-label required">Formats</div>
                    @foreach (\App\Domain\Consultation\Enums\ConsultationMode::cases() as $mode)
                        <label class="form-check form-check-inline">
                            <input type="checkbox" name="modes[]" value="{{ $mode->value }}" class="form-check-input" @checked(in_array($mode->value, old('modes', $professional->modes ?? []), true))>
                            <span class="form-check-label">{{ $mode->label() }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="col-12">
                    <label class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $professional->is_active ?? true))>
                        <span class="form-check-label">Profil actif (réservable une fois vérifié)</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('backoffice.professionals.index') }}" class="btn">Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</x-backoffice.page>
