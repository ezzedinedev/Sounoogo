@php($key = $lesson->id ?? 'new')
<form method="post" action="{{ $action }}" class="grid gap-4">
    @csrf
    @if ($method !== 'post') @method($method) @endif
    <div class="grid gap-4 sm:grid-cols-4">
        <div class="sm:col-span-3"><x-admin.input :id="'title-'.$key" name="title" label="Titre" :value="$lesson->title" required /></div>
        <x-admin.input :id="'duration-'.$key" name="duration_minutes" type="number" min="1" label="Durée (min)" :value="$lesson->duration_minutes" required />
    </div>
    <x-admin.textarea :id="'content-'.$key" name="content" label="Contenu (Markdown)" :value="$lesson->content" rows="6" required />
    <x-admin.input :id="'video-'.$key" name="video_url" type="url" label="Vidéo (URL)" :value="$lesson->video_url" />
    <div class="flex items-center justify-between gap-4">
        <x-admin.switch name="is_preview" label="Leçon d'aperçu (gratuite)" :checked="$lesson->is_preview" />
        <x-admin.button>{{ $method === 'post' ? 'Ajouter' : 'Enregistrer' }}</x-admin.button>
    </div>
</form>
