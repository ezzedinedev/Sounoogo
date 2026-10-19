@php($editing = $course->exists)
<x-admin.page :title="$editing ? $course->title : 'Nouvelle formation'" description="Informations du parcours, puis ses leçons.">
    @if ($editing)
        <x-slot:actions>
            <x-admin.button type="button" variant="outline" icon="trash" class="text-destructive" data-hs-overlay="#delete-course">Supprimer</x-admin.button>
        </x-slot:actions>
    @endif

    <div class="grid gap-4 lg:grid-cols-5">
        <form method="post" action="{{ $editing ? route('backoffice.courses.update', $course) : route('backoffice.courses.store') }}" class="lg:col-span-2">
            @csrf
            @if ($editing) @method('put') @endif
            <x-admin.card title="Informations">
                <div class="grid gap-5">
                    <x-admin.input name="title" label="Titre" :value="$course->title" required />
                    <x-admin.input name="slug" label="Adresse (slug)" :value="$course->slug" placeholder="générée depuis le titre" />
                    <x-admin.textarea name="summary" label="Résumé" :value="$course->summary" rows="2" maxlength="300" required />
                    <x-admin.textarea name="description" label="Description" :value="$course->description" rows="5" />
                    <x-admin.input name="cover_path" label="Image de couverture" :value="$course->cover_path" placeholder="images/courses/… ou URL" />
                    <div class="grid grid-cols-2 gap-4">
                        <x-admin.select name="level" label="Niveau" :options="\App\Domain\Learning\Models\Course::LEVELS" :value="$course->level" required />
                        <x-admin.input name="price" type="number" min="0" step="500" label="Prix (F CFA)" :value="$course->price" required />
                    </div>
                    <x-admin.switch name="included_in_subscription" label="Incluse dans l'abonnement" :checked="$course->included_in_subscription" />
                    <x-admin.switch name="published" label="Publiée" description="Visible sur le site et dans l'application." :checked="$course->published_at !== null" />
                </div>
                <x-slot:footer><x-admin.button class="ms-auto">Enregistrer</x-admin.button></x-slot:footer>
            </x-admin.card>
        </form>

        <div class="grid content-start gap-4 lg:col-span-3">
            @if ($editing)
                <x-admin.card title="Leçons" :description="$course->lessons->count().' leçon'.($course->lessons->count() > 1 ? 's' : '')" flush>
                    @forelse ($course->lessons as $lesson)
                        <details class="group border-t first:border-t-0">
                            <summary class="flex cursor-pointer list-none items-center gap-3 px-6 py-3 text-sm hover:bg-muted/40">
                                <span class="inline-flex size-6 items-center justify-center rounded-md bg-muted text-xs font-medium tabular-nums">{{ $lesson->position }}</span>
                                <span class="flex-1 font-medium">{{ $lesson->title }}</span>
                                <span class="text-xs text-muted-foreground">{{ $lesson->duration_minutes }} min</span>
                                @if ($lesson->is_preview)<x-admin.badge variant="primary">Aperçu</x-admin.badge>@endif
                                <x-icon name="chevron-down" :size="16" class="text-muted-foreground transition-transform group-open:rotate-180" />
                            </summary>
                            <div class="border-t bg-muted/20 px-6 py-5">
                                @include('backoffice.courses.partials.lesson-form', ['action' => route('backoffice.courses.lessons.update', [$course, $lesson]), 'method' => 'put', 'lesson' => $lesson])
                                <form method="post" action="{{ route('backoffice.courses.lessons.destroy', [$course, $lesson]) }}" class="mt-3" onsubmit="return confirm('Supprimer cette leçon ?')">
                                    @csrf @method('delete')
                                    <x-admin.button variant="ghost" size="sm" class="text-destructive" icon="trash">Supprimer la leçon</x-admin.button>
                                </form>
                            </div>
                        </details>
                    @empty
                        <x-admin.empty icon="list" title="Aucune leçon" subtitle="Ajoute la première leçon ci-dessous." />
                    @endforelse
                </x-admin.card>
                <x-admin.card title="Ajouter une leçon">
                    @include('backoffice.courses.partials.lesson-form', ['action' => route('backoffice.courses.lessons.store', $course), 'method' => 'post', 'lesson' => new \App\Domain\Learning\Models\Lesson(['duration_minutes' => 5])])
                </x-admin.card>
            @else
                <x-admin.card><x-admin.empty icon="list" title="Enregistre la formation" subtitle="Tu pourras ensuite ajouter ses leçons." /></x-admin.card>
            @endif
        </div>
    </div>

    @if ($editing)
        <x-admin.modal id="delete-course" title="Supprimer cette formation ?" description="Cette action est définitive. Si des membres la suivent, dépublie-la plutôt.">
            <form method="post" action="{{ route('backoffice.courses.destroy', $course) }}" class="flex justify-end gap-2 px-6 py-6">
                @csrf @method('delete')
                <x-admin.button type="button" variant="outline" data-hs-overlay="#delete-course">Annuler</x-admin.button>
                <x-admin.button variant="destructive">Supprimer</x-admin.button>
            </form>
        </x-admin.modal>
    @endif
</x-admin.page>
