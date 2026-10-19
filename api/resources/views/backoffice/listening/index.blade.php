@php($topics = ['stress' => 'Stress', 'anxiety' => 'Anxiété', 'sadness' => 'Tristesse', 'relationship' => 'Relation', 'family' => 'Famille', 'grief' => 'Deuil', 'violence' => 'Violence', 'self_esteem' => 'Estime de soi', 'other' => 'Autre'])
<x-backoffice.page title="Écoute" pretitle="Niveau 1">
    <div class="row row-cards" data-live-queue>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">File d'attente</h3>
                    <div class="card-actions"><span class="badge bg-yellow-lt">{{ $queue->count() }}</span></div>
                </div>
                @if ($queue->isEmpty())
                    <div class="card-body"><x-backoffice.empty icon="mood-smile" title="Personne n'attend" subtitle="Les nouvelles conversations apparaissent ici en temps réel." /></div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($queue as $conversation)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="d-flex align-items-center gap-2">
                                            <strong>{{ $conversation->member->pseudonym }}</strong>
                                            @if ($conversation->risk_level->value !== 'none')<x-backoffice.status :value="$conversation->risk_level" />@endif
                                            @if ($conversation->topic)<span class="badge bg-secondary-lt">{{ $topics[$conversation->topic] ?? $conversation->topic }}</span>@endif
                                        </div>
                                        <div class="text-secondary small mt-1">Attend depuis {{ $conversation->created_at->diffForHumans(syntax: \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <form method="post" action="{{ route('backoffice.listening.take', $conversation) }}">
                                            @csrf
                                            <button class="btn btn-primary btn-sm" type="submit">Prendre</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Mes conversations en cours</h3></div>
                @if ($mine->isEmpty())
                    <div class="card-body"><x-backoffice.empty icon="message-circle" title="Aucune conversation en cours" /></div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($mine as $conversation)
                            <a href="{{ route('backoffice.listening.show', $conversation) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $conversation->member->pseudonym }}</strong>
                                    <span class="small text-secondary">{{ $conversation->updated_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-secondary text-truncate">{{ $conversation->latestMessage?->body }}</div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($supervised->isNotEmpty())
                <div class="card mt-3">
                    <div class="card-header"><h3 class="card-title">Supervision · conversations à risque</h3></div>
                    <div class="list-group list-group-flush">
                        @foreach ($supervised as $conversation)
                            <a href="{{ route('backoffice.listening.show', $conversation) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>{{ $conversation->member->pseudonym }} <span class="text-secondary">avec {{ $conversation->listener?->displayName() }}</span></span>
                                <x-backoffice.status :value="$conversation->risk_level" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-backoffice.page>
