@php($editing = $exercise->exists)
@php($mode = old('mode', $exercise->breathing ? 'breathing' : 'steps'))
@php($breathing = $exercise->breathing ?? ['inhale' => 4, 'hold' => 2, 'exhale' => 6, 'rest' => 0, 'cycles' => 8])
<x-admin.page :title="$editing ? $exercise->title : 'Nouvel exercice'" description="Étapes minutées ou respiration rythmée ; un audio peut l'accompagner.">
    @if ($editing)
        <x-slot:actions>
            <form method="post" action="{{ route('backoffice.exercises.destroy', $exercise) }}" onsubmit="return confirm('Supprimer cet exercice ?')">
                @csrf @method('delete')
                <x-admin.button variant="outline" icon="trash" class="text-destructive">Supprimer</x-admin.button>
            </form>
        </x-slot:actions>
    @endif

    <form method="post" enctype="multipart/form-data" action="{{ $editing ? route('backoffice.exercises.update', $exercise) : route('backoffice.exercises.store') }}" class="grid gap-4 lg:grid-cols-5">
        @csrf
        @if ($editing) @method('put') @endif
        <x-admin.card title="Informations" class="lg:col-span-2">
            <div class="grid gap-5">
                <x-admin.input name="title" label="Titre" :value="$exercise->title" required />
                <x-admin.input name="slug" label="Adresse (slug)" :value="$exercise->slug" placeholder="générée depuis le titre" />
                <x-admin.textarea name="summary" label="Résumé" :value="$exercise->summary" rows="2" maxlength="300" required />
                <div class="grid grid-cols-2 gap-4">
                    <x-admin.select name="category" label="Catégorie" :options="collect(\App\Domain\Content\Enums\ExerciseCategory::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all()" :value="$exercise->category?->value" required />
                    <x-admin.select name="language" label="Langue" :options="['fr' => 'Français', 'mos' => 'Mooré', 'dyu' => 'Dioula']" :value="$exercise->language" required />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <x-admin.input name="duration_minutes" type="number" min="1" max="60" label="Durée (min)" :value="$exercise->duration_minutes" required />
                    <x-admin.input name="position" type="number" min="0" label="Ordre" :value="$exercise->position" />
                </div>
                <x-admin.switch name="published" label="Publié" description="Visible dans l'application." :checked="$exercise->published_at !== null" />
            </div>
            <x-slot:footer><x-admin.button class="ms-auto">Enregistrer</x-admin.button></x-slot:footer>
        </x-admin.card>

        <div class="grid content-start gap-4 lg:col-span-3">
            <x-admin.card title="Déroulé">
                <fieldset class="grid gap-5">
                    <legend class="sr-only">Format</legend>
                    <div class="inline-flex w-fit rounded-lg bg-muted p-1 text-sm">
                        @foreach (['steps' => 'Étapes guidées', 'breathing' => 'Respiration rythmée'] as $value => $label)
                            <label class="cursor-pointer rounded-md px-3 py-1.5 font-medium has-checked:bg-background has-checked:shadow-xs">
                                <input type="radio" name="mode" value="{{ $value }}" class="sr-only" @checked($mode === $value)>{{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <x-admin.textarea name="steps_text" label="Étapes" rows="10" :value="\App\Http\Controllers\Backoffice\ExerciseController::stepsText($exercise->steps)"
                        hint="Une étape par ligne. Durée en secondes après une barre : « Inspire lentement | 20 » (30 s par défaut)." />
                    <div class="grid grid-cols-5 gap-3">
                        @foreach (['inhale' => 'Inspirer', 'hold' => 'Retenir', 'exhale' => 'Expirer', 'rest' => 'Pause', 'cycles' => 'Cycles'] as $key => $label)
                            <x-admin.input :name="'breathing['.$key.']'" type="number" min="0" max="40" :label="$label" :value="$breathing[$key]" />
                        @endforeach
                    </div>
                    <p class="text-xs text-muted-foreground">Le rythme respiratoire n'est utilisé que pour le format « Respiration rythmée » (durées en secondes).</p>
                </fieldset>
            </x-admin.card>
            <x-admin.card title="Audio" description="Facultatif : une voix qui guide l'exercice (MP3, M4A ou OGG, 20 Mo au plus). Le membre peut le télécharger pour l'écouter hors ligne.">
                <div class="grid gap-3">
                    @if ($exercise->audio_path)
                        <audio controls preload="none" src="{{ $exercise->audioUrl() }}" class="w-full"></audio>
                        <x-admin.switch name="remove_audio" label="Retirer l'audio" />
                    @endif
                    <input type="file" name="audio" accept="audio/*" class="block w-full text-sm file:me-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-2 file:text-sm file:font-medium">
                    @error('audio')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </div>
            </x-admin.card>
        </div>
    </form>
</x-admin.page>
