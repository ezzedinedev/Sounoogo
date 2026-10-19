<x-backoffice.page title="Formations" pretitle="Niveau 3">
    <x-slot:actions>
        <a href="{{ route('backoffice.courses.create') }}" class="btn btn-primary"><i class="ti ti-plus icon"></i>Nouvelle formation</a>
    </x-slot:actions>

    <div class="card">
        @if ($courses->isEmpty())
            <div class="card-body"><x-backoffice.empty icon="school" title="Aucune formation" subtitle="Crée un premier parcours puis ajoute ses leçons." /></div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Formation</th><th>Niveau</th><th>Prix</th><th>Leçons</th><th>Inscrits</th><th>Publication</th><th class="w-1"></th></tr></thead>
                    <tbody>
                        @foreach ($courses as $course)
                            <tr>
                                <td>
                                    <div>{{ $course->title }}</div>
                                    <div class="small text-secondary text-truncate w-75">{{ $course->summary }}</div>
                                </td>
                                <td>{{ \App\Domain\Learning\Models\Course::LEVELS[$course->level] ?? $course->level }}</td>
                                <td>@if ($course->price === 0)<span class="badge bg-green-lt">Gratuite</span>@else<x-backoffice.money :amount="$course->price" />@endif</td>
                                <td>{{ $course->lessons_count }}</td>
                                <td>{{ $course->enrollments_count }}</td>
                                <td>@if ($course->published_at)<span class="badge bg-green-lt">Publiée</span>@else<span class="badge bg-secondary-lt">Brouillon</span>@endif</td>
                                <td><a href="{{ route('backoffice.courses.edit', $course) }}" class="btn btn-sm">Modifier</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $courses->links() }}</div>
        @endif
    </div>
</x-backoffice.page>
