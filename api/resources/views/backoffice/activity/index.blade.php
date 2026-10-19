<x-backoffice.page title="Journal d'activité" pretitle="Traçabilité">
    <div class="card">
        @if ($activities->isEmpty())
            <div class="card-body"><x-backoffice.empty icon="history" title="Aucune activité enregistrée" /></div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Date</th><th>Domaine</th><th>Action</th><th>Par</th></tr></thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            <tr>
                                <td class="text-secondary text-nowrap">{{ $activity->created_at->translatedFormat('j M Y, H:i') }}</td>
                                <td><span class="badge bg-secondary-lt">{{ $activity->log_name }}</span></td>
                                <td>{{ $activity->description }}</td>
                                <td>{{ $activity->causer?->displayName() ?? 'Système' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $activities->links() }}</div>
        @endif
    </div>
</x-backoffice.page>
