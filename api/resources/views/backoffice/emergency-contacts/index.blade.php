<x-backoffice.page title="Numéros d'urgence" pretitle="Protocole d'urgence">
    <div class="alert alert-info">Ces numéros s'affichent sur le site, dans l'application et lors de chaque alerte. Vérifie-les régulièrement auprès des autorités locales.</div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Pays</th><th>Service</th><th>Numéro</th><th>Description</th><th>Ordre</th><th>Actif</th><th class="w-1"></th></tr></thead>
                <tbody>
                    @foreach ($contacts as $contact)
                        <tr>
                            <form method="post" action="{{ route('backoffice.emergency-contacts.update', $contact) }}" id="contact-{{ $contact->id }}">@csrf @method('put')</form>
                            <td><input form="contact-{{ $contact->id }}" name="country" value="{{ $contact->country }}" class="form-control form-control-sm" maxlength="2" size="2" aria-label="Pays" required></td>
                            <td><input form="contact-{{ $contact->id }}" name="label" value="{{ $contact->label }}" class="form-control form-control-sm" aria-label="Service" required></td>
                            <td><input form="contact-{{ $contact->id }}" name="phone" value="{{ $contact->phone }}" class="form-control form-control-sm" aria-label="Numéro" required></td>
                            <td><input form="contact-{{ $contact->id }}" name="description" value="{{ $contact->description }}" class="form-control form-control-sm" aria-label="Description"></td>
                            <td><input form="contact-{{ $contact->id }}" type="number" name="position" value="{{ $contact->position }}" class="form-control form-control-sm" min="0" aria-label="Ordre" required></td>
                            <td>
                                <input form="contact-{{ $contact->id }}" type="hidden" name="is_active" value="0">
                                <input form="contact-{{ $contact->id }}" type="checkbox" name="is_active" value="1" class="form-check-input" @checked($contact->is_active) aria-label="Actif">
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <button form="contact-{{ $contact->id }}" class="btn btn-sm btn-primary" type="submit">Enregistrer</button>
                                    <form method="post" action="{{ route('backoffice.emergency-contacts.destroy', $contact) }}" onsubmit="return confirm('Supprimer ce numéro ?')">
                                        @csrf @method('delete')
                                        <button class="btn btn-sm btn-ghost-danger" type="submit" aria-label="Supprimer"><i class="ti ti-trash icon"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <form method="post" action="{{ route('backoffice.emergency-contacts.store') }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-1"><label class="form-label" for="new-country">Pays</label><input id="new-country" name="country" value="BF" maxlength="2" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label" for="new-label">Service</label><input id="new-label" name="label" class="form-control" required></div>
                <div class="col-md-2"><label class="form-label" for="new-phone">Numéro</label><input id="new-phone" name="phone" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label" for="new-description">Description</label><input id="new-description" name="description" class="form-control"></div>
                <div class="col-md-1"><label class="form-label" for="new-position">Ordre</label><input type="number" id="new-position" name="position" value="{{ $contacts->count() }}" min="0" class="form-control" required></div>
                <input type="hidden" name="is_active" value="1">
                <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="ti ti-plus icon"></i>Ajouter</button></div>
            </form>
        </div>
    </div>
</x-backoffice.page>
