{{-- Bulle de message. Avec $message = null, sert de gabarit au temps réel (resources/js/admin.js). --}}
@if ($message?->kind === 'system')
    <div class="chat-bubble-system my-3" data-message-id="{{ $message->id }}">{{ $message->body }}</div>
@else
    @php($mine = $message && $message->sender_id === auth()->id())
    @php($fromMember = $message && $message->sender_id === $conversation->member_id)
    <div class="chat-item mb-3" data-message-id="{{ $message?->id }}">
        <div @class(['row align-items-end', 'justify-content-end' => $mine])>
            <div class="col-lg-9">
                <div @class(['chat-bubble', 'chat-bubble-me' => $mine, 'border border-danger' => $message?->is_flagged])>
                    <div class="chat-bubble-title">
                        <div class="row">
                            <div class="col chat-bubble-author" data-author>{{ $message?->sender?->displayName() ?? 'SUNOOGO' }}</div>
                            <div class="col-auto chat-bubble-date" data-time>{{ $message?->created_at->format('H:i') }}</div>
                        </div>
                    </div>
                    <div class="chat-bubble-body"><p class="mb-0" data-body>{{ $message?->body }}</p></div>
                    @if ($message?->is_flagged && $fromMember)
                        <div class="small text-danger mt-1"><i class="ti ti-alert-triangle"></i> Signal de détresse détecté</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
