@php($editing = $course->exists)
<x-backoffice.page :title="$editing ? $course->title : 'Nouvelle formation'" pretitle="Formations">
    @if ($editing)
        <x-slot:actions>
            <form method="post" action="{{ route('backoffice.courses.destroy', $course) }}" onsubmit="return confirm('Supprimer définitivement cette formation ?')">
                @csrf @method('delete')
                <button class="btn btn-outline-danger" type="submit"><i class="ti ti-trash icon"></i>Supprimer</button>
            </form>
        </x-slot:actions>
    @endif

    <div class="row row-cards">
        <div class="col-lg-5">
            <form method="post" action="{{ $editing ? route('backoffice.courses.update', $course) : route('backoffice.courses.store') }}" class="card">
                @csrf
                @if ($editing) @method('put') @endif
                <div class="card-header"><h3 class="card-title">Informations</h3></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required" for="title">Titre</label>
                        <input id="title" name="title" value="{{ old('title', $course->title) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">Adresse (slug)</label>
                        <input id="slug" name="slug" value="{{ old('slug', $course->slug) }}" class="form-control" placeholder="générée depuis le titre">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required" for="summary">Résumé</label>
                        <textarea id="summary" name="summary" rows="2" maxlength="300" class="form-control" required>{{ old('summary', $course->summary) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" rows="5" class="form-control">{{ old('description', $course->description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="cover_path">Image de couverture</label>
                        <input id="cover_path" name="cover_path" value="{{ old('cover_path', $course->cover_path) }}" class="form-control" placeholder="images/courses/… ou URL">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label required" for="level">Niveau</label>
                            <select id="level" name="level" class="form-select">
                                @foreach (\App\Domain\Learning\Models\Course::LEVELS as $value => $label)
                                    <option value="{{ $value }}" @selected(old('level', $course->level) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label required" for="price">Prix (F CFA)</label>
                            <input type="number" id="price" name="price" min="0" step="500" value="{{ old('price', $course->price) }}" class="form-control" required>
                        </div>
                    </div>
                    <label class="form-check form-switch">
                        <input type="hidden" name="included_in_subscription" value="0">
                        <input type="checkbox" name="included_in_subscription" value="1" class="form-check-input" @checked(old('included_in_subscription', $course->included_in_subscription))>
                        <span class="form-check-label">Incluse dans l'abonnement</span>
                    </label>
                    <label class="form-check form-switch">
                        <input type="hidden" name="published" value="0">
                        <input type="checkbox" name="published" value="1" class="form-check-input" @checked(old('published', $course->published_at !== null))>
                        <span class="form-check-label">Publiée</span>
                    </label>
                </div>
                <div class="card-footer text-end"><button class="btn btn-primary" type="submit">Enregistrer</button></div>
            </form>
        </div>

        <div class="col-lg-7">
            @if ($editing)
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Leçons</h3></div>
                    <div class="accordion accordion-flush" id="lessons">
                        @forelse ($course->lessons as $lesson)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lesson-{{ $lesson->id }}" aria-expanded="false">
                                        <span class="text-secondary me-2">{{ $lesson->position }}.</span> {{ $lesson->title }}
                                        <span class="ms-auto me-3 small text-secondary">{{ $lesson->duration_minutes }} min @if ($lesson->is_preview)· aperçu @endif</span>
                                    </button>
                                </h2>
                                <div id="lesson-{{ $lesson->id }}" class="accordion-collapse collapse" data-bs-parent="#lessons">
                                    <div class="accordion-body">
                                        @include('backoffice.courses.partials.lesson-form', ['action' => route('backoffice.courses.lessons.update', [$course, $lesson]), 'method' => 'put', 'lesson' => $lesson])
                                        <form method="post" action="{{ route('backoffice.courses.lessons.destroy', [$course, $lesson]) }}" class="mt-2" onsubmit="return confirm('Supprimer cette leçon ?')">
                                            @csrf @method('delete')
                                            <button class="btn btn-sm btn-ghost-danger" type="submit">Supprimer la leçon</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="card-body"><x-backoffice.empty icon="list" title="Aucune leçon" subtitle="Ajoute la première leçon ci-dessous." /></div>
                        @endforelse
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header"><h3 class="card-title">Ajouter une leçon</h3></div>
                    <div class="card-body">
                        @include('backoffice.courses.partials.lesson-form', ['action' => route('backoffice.courses.lessons.store', $course), 'method' => 'post', 'lesson' => new \App\Domain\Learning\Models\Lesson(['duration_minutes' => 5])])
                    </div>
                </div>
            @else
                <div class="card"><div class="card-body"><x-backoffice.empty icon="list" title="Enregistre la formation" subtitle="Tu pourras ensuite ajouter ses leçons." /></div></div>
            @endif
        </div>
    </div>
</x-backoffice.page>
