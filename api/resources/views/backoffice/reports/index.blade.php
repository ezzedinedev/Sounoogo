@php($resolutions = \App\Http\Controllers\Backoffice\ReportController::RESOLUTIONS)
<x-admin.page title="Signalements" description="Modération des comportements signalés par les membres et les écoutants.">
    <x-slot:actions>
        <div class="inline-flex rounded-lg bg-muted p-1 text-sm">
            @foreach (\App\Domain\Listening\Enums\ReportStatus::cases() as $case)
                <a href="{{ route('backoffice.reports.index', ['statut' => $case->value]) }}" @class(['rounded-md px-3 py-1.5 font-medium', 'bg-background shadow-xs' => $status === $case, 'text-muted-foreground hover:text-foreground' => $status !== $case])>{{ $case->label() }}</a>
            @endforeach
        </div>
    </x-slot:actions>

    <x-admin.card flush>
        @if ($reports->isEmpty())
            <x-admin.empty icon="flag" :title="'Aucun signalement « '.mb_strtolower($status->label()).' »'" />
        @else
            <x-admin.table>
                <thead><tr><th>Motif</th><th>Signalé par</th><th>Personne signalée</th><th>Message</th><th>Reçu</th><th class="text-end">Décision</th></tr></thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td>
                                <p class="font-medium">{{ $report->reasonLabel() }}</p>
                                @if ($report->details)<p class="mt-0.5 max-w-xs text-xs text-muted-foreground">{{ $report->details }}</p>@endif
                            </td>
                            <td>{{ $report->reporter->displayName() }}</td>
                            <td>
                                {{ $report->reportedUser?->displayName() ?? '—' }}
                                @if ($report->reportedUser?->isSuspended())<x-admin.badge variant="destructive" class="ms-1">Suspendu</x-admin.badge>@endif
                            </td>
                            <td class="max-w-56"><p class="truncate text-muted-foreground">{{ $report->message?->body ?? '—' }}</p></td>
                            <td class="text-muted-foreground whitespace-nowrap">{{ $report->created_at->diffForHumans() }}</td>
                            <td>
                                @if ($report->status->value === 'pending')
                                    <form method="post" action="{{ route('backoffice.reports.resolve', $report) }}" class="flex justify-end gap-2">
                                        @csrf
                                        <x-admin.select name="resolution" :id="'resolution-'.$report->id" :options="$resolutions" class="h-8 w-52" aria-label="Décision" />
                                        <x-admin.button size="sm">Valider</x-admin.button>
                                    </form>
                                @else
                                    <p class="text-end text-xs text-muted-foreground">{{ $resolutions[$report->resolution] ?? '' }} · {{ $report->moderator?->displayName() }}</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
        <x-slot:footer>@if ($reports->hasPages()){{ $reports->links() }}@endif</x-slot:footer>
    </x-admin.card>
</x-admin.page>
