@foreach (['status' => 'success', 'error' => 'danger'] as $key => $type)
    @if (session($key))
        <div class="alert alert-{{ $type }} alert-dismissible" role="alert">
            <div class="d-flex gap-2">
                <i class="ti ti-{{ $type === 'success' ? 'check' : 'alert-circle' }} icon alert-icon"></i>
                <div>{{ session($key) }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></a>
        </div>
    @endif
@endforeach
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="d-flex gap-2">
            <i class="ti ti-alert-circle icon alert-icon"></i>
            <div>
                <h4 class="alert-title">Vérifie le formulaire</h4>
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        </div>
    </div>
@endif
