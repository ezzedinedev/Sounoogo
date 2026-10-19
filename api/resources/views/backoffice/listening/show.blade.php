@php($participant = $conversation->involves(auth()->user()))
<x-admin.page :title="'Conversation avec '.$conversation->member->pseudonym" :description="'Ouverte le '.$conversation->created_at->translatedFormat('j F à H:i')">
    <x-slot:actions>
        <x-admin.button :href="route('backoffice.listening.index')" variant="outline" icon="arrow-left">Retour</x-admin.button>
        @if ($participant && $conversation->isOpen())
            <x-admin.button type="button" variant="outline" icon="circle-x" class="text-destructive" data-hs-overlay="#close-conversation">Clôturer</x-admin.button>
        @endif
    </x-slot:actions>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @if ($conversation->risk_level->value === 'high')
                <div role="alert" class="mb-4 flex items-start gap-3 rounded-lg border border-destructive/30 bg-destructive-soft px-4 py-3 text-sm">
                    <x-icon name="alert-triangle" :size="18" class="mt-px text-destructive" />
                    <div>
                        <p class="font-medium text-destructive">Risque élevé détecté</p>
                        <p class="mt-0.5 text-foreground/80">Les numéros d'urgence ont été affichés au membre et un superviseur a été alerté. Reste présent·e, pose des questions ouvertes et suis le protocole d'urgence.</p>
                    </div>
                </div>
            @endif

            <section class="flex h-[65vh] flex-col rounded-xl border bg-card shadow-xs">
                <div class="flex-1 overflow-y-auto px-6 py-6" data-autoscroll>
                    <div class="space-y-4" data-live-conversation="{{ $conversation->id }}" aria-live="polite">
                        @foreach ($messages as $message)
                            @include('backoffice.listening.partials.message', ['message' => $message])
                        @endforeach
                    </div>
                </div>
                @if ($participant && $conversation->isOpen())
                    <form method="post" action="{{ route('backoffice.listening.send', $conversation) }}" class="border-t p-4">
                        @csrf
                        <label for="body" class="sr-only">Message</label>
                        <div class="flex items-end gap-2 rounded-lg border border-input bg-background p-2 shadow-xs focus-within:border-ring focus-within:ring-[3px] focus-within:ring-ring/40">
                            <textarea id="body" name="body" rows="2" required maxlength="4000" placeholder="Écris ta réponse…" class="flex-1 resize-none border-0 bg-transparent px-2 py-1 text-sm focus:outline-none focus:ring-0"></textarea>
                            <x-admin.button icon="send">Envoyer</x-admin.button>
                        </div>
                    </form>
                @endif
            </section>

            <template id="message-template">
                @include('backoffice.listening.partials.message', ['message' => null])
            </template>
        </div>

        <div class="grid content-start gap-4">
            <x-admin.card title="Détails">
                <dl class="grid gap-4 text-sm">
                    <div class="flex items-center justify-between"><dt class="text-muted-foreground">État</dt><dd><x-admin.status :value="$conversation->status" /></dd></div>
                    <div class="flex items-center justify-between"><dt class="text-muted-foreground">Risque</dt><dd><x-admin.status :value="$conversation->risk_level" /></dd></div>
                    <div class="flex items-center justify-between"><dt class="text-muted-foreground">Écoutant</dt><dd class="font-medium">{{ $conversation->listener?->displayName() ?? '—' }}</dd></div>
                    @if ($conversation->member_rating)
                        <div class="flex items-center justify-between"><dt class="text-muted-foreground">Note du membre</dt><dd class="font-medium tabular-nums">{{ $conversation->member_rating }}/5</dd></div>
                    @endif
                </dl>
            </x-admin.card>
            @if (config('sunoogo.ai.enabled'))
                <x-admin.card title="Copilote" description="Des pistes à relire et adapter : rien n'est envoyé sans toi."
                    data-copilot data-summary-url="{{ route('backoffice.copilot.summary', $conversation) }}"
                    data-suggest-url="{{ $participant && $conversation->isOpen() ? route('backoffice.copilot.suggestions', $conversation) : '' }}">
                    <div class="flex flex-wrap gap-2">
                        @if ($participant && $conversation->isOpen())
                            <x-admin.button type="button" variant="outline" size="sm" icon="bulb" data-copilot-action="suggest">Suggérer une réponse</x-admin.button>
                        @endif
                        <x-admin.button type="button" variant="outline" size="sm" icon="file-text" data-copilot-action="summary">Résumer</x-admin.button>
                    </div>
                    <div class="mt-4 grid gap-2 text-sm empty:hidden" data-copilot-output aria-live="polite"></div>
                </x-admin.card>
            @endif
            <x-admin.card title="Repères d'écoute">
                <ul class="grid gap-3 text-sm text-muted-foreground">
                    <li class="flex gap-2"><x-icon name="check" :size="16" class="mt-0.5 text-primary" />Reformule, ne juge pas, ne conseille pas trop vite.</li>
                    <li class="flex gap-2"><x-icon name="check" :size="16" class="mt-0.5 text-primary" />Ne demande jamais d'information identifiante.</li>
                    <li class="flex gap-2"><x-icon name="check" :size="16" class="mt-0.5 text-primary" />Pensées suicidaires : demande-le clairement, puis préviens un superviseur.</li>
                </ul>
            </x-admin.card>
        </div>
    </div>

    @if ($participant && $conversation->isOpen())
        <x-admin.modal id="close-conversation" title="Clôturer la conversation ?" description="Le membre sera informé que la conversation est terminée. Il pourra en ouvrir une nouvelle à tout moment.">
            <form method="post" action="{{ route('backoffice.listening.close', $conversation) }}" class="flex justify-end gap-2 px-6 py-6">
                @csrf
                <x-admin.button type="button" variant="outline" data-hs-overlay="#close-conversation">Annuler</x-admin.button>
                <x-admin.button variant="destructive">Clôturer</x-admin.button>
            </form>
        </x-admin.modal>
    @endif
</x-admin.page>
