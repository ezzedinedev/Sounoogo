<x-backoffice.page title="Nouveau compte équipe" pretitle="Utilisateurs">
    <div class="row">
        <div class="col-lg-7">
            <form method="post" action="{{ route('backoffice.users.store') }}" class="card">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required" for="name">Nom complet</label>
                        <input id="name" name="name" value="{{ old('name') }}" @class(['form-control', 'is-invalid' => $errors->has('name')]) required>
                    </div>
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label required" for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" @class(['form-control', 'is-invalid' => $errors->has('email')]) required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label" for="phone">Téléphone</label>
                            <input id="phone" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="+226…">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-label required">Rôle</div>
                        <div class="form-selectgroup">
                            @foreach ($roles as $role)
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="role" value="{{ $role->value }}" class="form-selectgroup-input" @checked(old('role', 'listener') === $role->value)>
                                    <span class="form-selectgroup-label">{{ $role->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required" for="password">Mot de passe provisoire</label>
                        <input type="text" id="password" name="password" class="form-control" minlength="10" required autocomplete="new-password">
                        <small class="form-hint">10 caractères minimum. À transmettre par un canal sûr et à changer à la première connexion.</small>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('backoffice.users.index') }}" class="btn">Annuler</a>
                    <button class="btn btn-primary" type="submit">Créer le compte</button>
                </div>
            </form>
        </div>
    </div>
</x-backoffice.page>
