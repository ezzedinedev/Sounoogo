<x-admin.page title="Formations" description="Parcours de confiance en soi et d'indépendance affective.">
    <x-slot:actions>
        <x-admin.button :href="route('backoffice.courses.create')" icon="plus">Nouvelle formation</x-admin.button>
    </x-slot:actions>

    <x-admin.card flush>
        @if ($courses->isEmpty())
            <x-admin.empty icon="school" title="Aucune formation" subtitle="Crée un premier parcours puis ajoute ses leçons." />
        @else
            <x-admin.table>
                <thead><tr><th>Formation</th><th>Niveau</th><th class="text-end">Prix</th><th class="text-end">Leçons</th><th class="text-end">Inscrits</th><th>Publication</th><th></th></tr></thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td class="max-w-md">
                                <p class="font-medium">{{ $course->title }}</p>
                                <p class="truncate text-xs text-muted-foreground">{{ $course->summary }}</p>
                            </td>
                            <td>{{ \App\Domain\Learning\Models\Course::LEVELS[$course->level] ?? $course->level }}</td>
                            <td class="text-end">@if ($course->price === 0)<x-admin.badge variant="success">Gratuite</x-admin.badge>@else<x-admin.money :amount="$course->price" />@endif</td>
                            <td class="text-end tabular-nums">{{ $course->lessons_count }}</td>
                            <td class="text-end tabular-nums">{{ $course->enrollments_count }}</td>
                            <td>@if ($course->published_at)<x-admin.badge variant="success">Publiée</x-admin.badge>@else<x-admin.badge>Brouillon</x-admin.badge>@endif</td>
                            <td class="text-end"><x-admin.button :href="route('backoffice.courses.edit', $course)" variant="outline" size="sm">Modifier</x-admin.button></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
        <x-slot:footer>@if ($courses->hasPages()){{ $courses->links() }}@endif</x-slot:footer>
    </x-admin.card>
</x-admin.page>
