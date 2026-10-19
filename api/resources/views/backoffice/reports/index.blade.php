<x-backoffice.page title="Signalements" pretitle="Modération">
    <x-slot:actions>
        <div class="btn-group" role="group" aria-label="Filtrer par état">
            @foreach (\App\Domain\Listening\Enums\ReportStatus::cases() as $case)
                <a href="{{ route('backoffice.reports.index', ['statut' => $case->value]) }}" @class(['btn', 'active' => $status === $case])>{{ $case->label() }}</a>
            @endforeach
        </div>
    </x-slot:actions>

    <div class="card">
        @if ($reports->isEmpty())
            <div class="card-body"><x-backoffice.empty icon="flag" title="Aucun signalement {{ mb_strtolower($status->label()) }}" /></div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Motif</th><th>Signalé par</th><th>Personne signalée</th><th>Message</th><th>Reçu</th><th class="w-1"></th></tr></thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>
                                    <strong>{{ $report->reasonLabel() }}</strong>
                                    @if ($report->details)<div class="small text-secondary">{{ $report->details }}</div>@endif
                                </td>
                                <td>{{ $report->reporter->displayName() }}</td>
                                <td>
                                    {{ $report->reportedUser?->displayName() ?? '—' }}
                                    @if ($report->reportedUser?->isSuspended())<span class="badge bg-red-lt ms-1">Suspendu</span>@endif
                                </td>
                                <td class="text-secondary w-25"><div class="text-truncate">{{ $report->message?->body ?? '—' }}</div></td>
                                <td class="text-secondary">{{ $report->created_at->diffForHumans() }}</td>
                                <td>
                                    @if ($report->status->value === 'pending')
                                        <form method="post" action="{{ route('backoffice.reports.resolve', $report) }}" class="d-flex gap-2">
                                            @csrf
                                            <select name="resolution" class="form-select form-select-sm" aria-label="Décision">
                                                @foreach (\App\Http\Controllers\Backoffice\ReportController::RESOLUTIONS as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-primary">Valider</button>
                                        </form>
                                    @else
                                        <span class="text-secondary small">{{ \App\Http\Controllers\Backoffice\ReportController::RESOLUTIONS[$report->resolution] ?? '' }} · {{ $report->moderator?->displayName() }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $reports->links() }}</div>
        @endif
    </div>
</x-backoffice.page>
