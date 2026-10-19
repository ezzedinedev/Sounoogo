{{-- Bulle de message. Avec $message = null, sert de gabarit au temps réel (resources/js/admin.js). --}}
@if ($message?->kind === 'system')
    <p class="py-1 text-center text-xs text-muted-foreground" data-message-id="{{ $message->id }}">{{ $message->body }}</p>
@else
    @php($mine = $message && $message->sender_id === auth()->id())
    @php($fromMember = $message && $message->sender_id === $conversation->member_id)
    <div @class(['flex', 'justify-end' => $mine]) data-message-id="{{ $message?->id }}">
        <div class="max-w-[80%]">
            <div @class(['mb-1 flex items-center gap-2 text-xs text-muted-foreground', 'justify-end' => $mine])>
                <span class="font-medium text-foreground" data-author>{{ $message?->sender?->displayName() ?? 'SUNOOGO' }}</span>
                <span data-time>{{ $message?->created_at->format('H:i') }}</span>
            </div>
            <p @class([
                'whitespace-pre-line rounded-2xl px-4 py-2.5 text-sm leading-relaxed',
                'rounded-se-md bg-primary text-primary-foreground' => $mine,
                'rounded-ss-md bg-muted' => ! $mine,
                'ring-2 ring-destructive/50' => $message?->is_flagged,
            ]) data-body>{{ $message?->body }}</p>
            @if ($message?->isVoice())
                <audio controls preload="none" class="mt-2 h-10 w-72 max-w-full" src="{{ route('backoffice.listening.audio', [$conversation, $message]) }}"></audio>
            @endif
            @if ($message?->is_flagged && $fromMember)
                <p class="mt-1 inline-flex items-center gap-1 text-xs font-medium text-destructive"><x-icon name="alert-triangle" :size="14" />Signal de détresse détecté</p>
            @endif
        </div>
    </div>
@endif
