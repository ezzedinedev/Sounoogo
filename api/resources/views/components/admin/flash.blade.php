@foreach (['status' => 'success', 'error' => 'destructive'] as $key => $variant)
    @if (session($key))
        <div role="alert" @class([
            'mb-6 flex items-start gap-3 rounded-lg border px-4 py-3 text-sm',
            'border-success/30 bg-success-soft text-success' => $variant === 'success',
            'border-destructive/30 bg-destructive-soft text-destructive' => $variant === 'destructive',
        ])>
            <x-icon :name="$variant === 'success' ? 'circle-check' : 'alert-circle'" :size="18" class="mt-px" />
            <p class="text-foreground">{{ session($key) }}</p>
        </div>
    @endif
@endforeach
@if ($errors->any())
    <div role="alert" class="mb-6 flex items-start gap-3 rounded-lg border border-destructive/30 bg-destructive-soft px-4 py-3 text-sm text-destructive">
        <x-icon name="alert-circle" :size="18" class="mt-px" />
        <div class="text-foreground">
            <p class="font-medium">Vérifie le formulaire</p>
            <ul class="mt-1 list-disc ps-4 text-muted-foreground">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    </div>
@endif
