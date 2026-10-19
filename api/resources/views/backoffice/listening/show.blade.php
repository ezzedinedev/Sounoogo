@php($participant = $conversation->involves(auth()->user()))
<x-backoffice.page :title="'Conversation avec '.$conversation->member->pseudonym" pretitle="Écoute">
    <x-slot:actions>
        <a href="{{ route('backoffice.listening.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left icon"></i>Retour</a>
        @if ($participant && $conversation->isOpen())
            <form method="post" action="{{ route('backoffice.listening.close', $conversation) }}" onsubmit="return confirm('Clôturer cette conversation ?')">
                @csrf
                <button class="btn btn-outline-danger" type="submit"><i class="ti ti-circle-x icon"></i>Clôturer</button>
            </form>
        @endif
    </x-slot:actions>

    <div class="row row-cards">
        <div class="col-lg-8">
            @if ($conversation->risk_level->value === 'high')
                <div class="alert alert-danger" role="alert">
                    <div class="d-flex gap-2">
                        <i class="ti ti-alert-triangle icon alert-icon"></i>
                        <div>
                            <h4 class="alert-title">Risque élevé détecté</h4>
                            <div class="text-secondary">Les numéros d'urgence ont été affichés au membre et un superviseur a été alerté. Reste présent·e, pose des questions ouvertes, et suis le protocole d'urgence.</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body chat-thread" data-autoscroll>
                    <div class="chat">
                        <div class="chat-bubbles" data-live-conversation="{{ $conversation->id }}" aria-live="polite">
                            @foreach ($messages as $message)
                                @include('backoffice.listening.partials.message', ['message' => $message])
                            @endforeach
                        </div>
                    </div>
                </div>
                @if ($participant && $conversation->isOpen())
                    <div class="card-footer">
                        <form method="post" action="{{ route('backoffice.listening.send', $conversation) }}">
                            @csrf
                            <label class="visually-hidden" for="body">Message</label>
                            <div class="input-group">
                                <textarea id="body" name="body" rows="2" class="form-control" placeholder="Écris ta réponse…" required maxlength="4000"></textarea>
                                <button class="btn btn-primary" type="submit"><i class="ti ti-send icon"></i>Envoyer</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <template id="message-template">
                @include('backoffice.listening.partials.message', ['message' => null])
            </template>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item"><div class="datagrid-title">État</div><div class="datagrid-content"><x-backoffice.status :value="$conversation->status" /></div></div>
                        <div class="datagrid-item"><div class="datagrid-title">Niveau de risque</div><div class="datagrid-content"><x-backoffice.status :value="$conversation->risk_level" /></div></div>
                        <div class="datagrid-item"><div class="datagrid-title">Écoutant</div><div class="datagrid-content">{{ $conversation->listener?->displayName() ?? '—' }}</div></div>
                        <div class="datagrid-item"><div class="datagrid-title">Ouverte</div><div class="datagrid-content">{{ $conversation->created_at->translatedFormat('j M, H:i') }}</div></div>
                        @if ($conversation->member_rating)
                            <div class="datagrid-item"><div class="datagrid-title">Note du membre</div><div class="datagrid-content">{{ $conversation->member_rating }}/5</div></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Repères d'écoute</h3></div>
                <div class="card-body">
                    <ul class="mb-0 ps-3 text-secondary">
                        <li>Reformule, ne juge pas, ne conseille pas trop vite.</li>
                        <li>Ne demande jamais d'information identifiante.</li>
                        <li>Pensées suicidaires : demande-le clairement, puis préviens un superviseur.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-backoffice.page>
