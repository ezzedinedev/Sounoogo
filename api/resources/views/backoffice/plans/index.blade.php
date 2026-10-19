<x-backoffice.page title="Offres d'abonnement" pretitle="Niveau 3">
    <div class="row row-cards">
        @foreach ($plans as $plan)
            <div class="col-lg-6">
                <form method="post" action="{{ route('backoffice.plans.update', $plan) }}" class="card">
                    @csrf @method('put')
                    <div class="card-header">
                        <h3 class="card-title">{{ $plan->name }} <span class="text-secondary small ms-1">{{ $plan->code }}</span></h3>
                        <div class="card-actions"><span class="badge bg-primary-lt">{{ $plan->subscriptions_count }} abonné·es actifs</span></div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name-{{ $plan->id }}">Nom</label>
                                <input id="name-{{ $plan->id }}" name="name" value="{{ $plan->name }}" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="price-{{ $plan->id }}">Prix / mois</label>
                                <input type="number" id="price-{{ $plan->id }}" name="price" value="{{ $plan->price }}" min="0" step="500" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="discount-{{ $plan->id }}">Remise consult. %</label>
                                <input type="number" id="discount-{{ $plan->id }}" name="consultation_discount" value="{{ $plan->consultation_discount }}" min="0" max="100" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="description-{{ $plan->id }}">Description</label>
                            <input id="description-{{ $plan->id }}" name="description" value="{{ $plan->description }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="features-{{ $plan->id }}">Avantages (un par ligne)</label>
                            <textarea id="features-{{ $plan->id }}" name="features" rows="4" class="form-control">{{ implode("\n", $plan->features ?? []) }}</textarea>
                        </div>
                        <label class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" @checked($plan->is_active)>
                            <span class="form-check-label">Proposée aux membres</span>
                        </label>
                    </div>
                    <div class="card-footer text-end"><button class="btn btn-primary" type="submit">Enregistrer</button></div>
                </form>
            </div>
        @endforeach
    </div>
</x-backoffice.page>
