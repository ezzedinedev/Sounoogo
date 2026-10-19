<x-admin.page title="Journal d'activité" description="Traçabilité des actions sensibles : supervision, modération, administration.">
    <x-admin.card flush>
        @if ($activities->isEmpty())
            <x-admin.empty icon="history" title="Aucune activité enregistrée" />
        @else
            <x-admin.table>
                <thead><tr><th>Date</th><th>Domaine</th><th>Action</th><th>Par</th></tr></thead>
                <tbody>
                    @foreach ($activities as $activity)
                        <tr>
                            <td class="text-muted-foreground whitespace-nowrap tabular-nums">{{ $activity->created_at->translatedFormat('j M Y, H:i') }}</td>
                            <td><x-admin.badge variant="outline">{{ $activity->log_name }}</x-admin.badge></td>
                            <td>{{ $activity->description }}</td>
                            <td class="font-medium">{{ $activity->causer?->displayName() ?? 'Système' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
        <x-slot:footer>@if ($activities->hasPages()){{ $activities->links() }}@endif</x-slot:footer>
    </x-admin.card>
</x-admin.page>
