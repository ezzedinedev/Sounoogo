@php($key = $lesson->id ?? 'new')
<form method="post" action="{{ $action }}">
    @csrf
    @if ($method !== 'post') @method($method) @endif
    <div class="row">
        <div class="col-md-8 mb-3">
            <label class="form-label required" for="title-{{ $key }}">Titre</label>
            <input id="title-{{ $key }}" name="title" value="{{ $lesson->title }}" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label required" for="duration-{{ $key }}">Durée (min)</label>
            <input type="number" id="duration-{{ $key }}" name="duration_minutes" min="1" value="{{ $lesson->duration_minutes }}" class="form-control" required>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label required" for="content-{{ $key }}">Contenu (Markdown)</label>
        <textarea id="content-{{ $key }}" name="content" rows="6" class="form-control" required>{{ $lesson->content }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label" for="video-{{ $key }}">Vidéo (URL)</label>
        <input type="url" id="video-{{ $key }}" name="video_url" value="{{ $lesson->video_url }}" class="form-control">
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <label class="form-check form-switch mb-0">
            <input type="hidden" name="is_preview" value="0">
            <input type="checkbox" name="is_preview" value="1" class="form-check-input" @checked($lesson->is_preview)>
            <span class="form-check-label">Leçon d'aperçu (gratuite)</span>
        </label>
        <button class="btn btn-primary" type="submit">{{ $method === 'post' ? 'Ajouter' : 'Enregistrer' }}</button>
    </div>
</form>
