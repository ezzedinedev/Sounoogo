<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Listening\Actions\SendMessage;
use App\Domain\Listening\Enums\ConversationStatus;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Services\ListenerQueue;
use App\Http\Controllers\Controller;
use App\Http\Requests\Listening\SendMessageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListeningController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('backoffice.listening.index', [
            'queue' => Conversation::query()
                ->where('status', ConversationStatus::Waiting)
                ->with(['member', 'latestMessage'])
                ->orderByRaw("case risk_level when 'high' then 0 when 'medium' then 1 else 2 end")
                ->oldest()
                ->get(),
            'mine' => $user->listenerConversations()
                ->with(['member', 'latestMessage'])
                ->where('status', ConversationStatus::Active)
                ->latest('updated_at')
                ->get(),
            'supervised' => $user->can('conversations.supervise')
                ? Conversation::with(['member', 'listener'])->where('status', ConversationStatus::Active)->where('risk_level', '!=', 'none')->latest('updated_at')->get()
                : collect(),
        ]);
    }

    public function availability(Request $request, ListenerQueue $queue): RedirectResponse
    {
        $available = ! $request->user()->is_available;
        $request->user()->update(['is_available' => $available]);

        if ($available && $conversation = $queue->takeNext($request->user())) {
            return redirect()->route('backoffice.listening.show', $conversation)->with('status', 'Tu es disponible. Une conversation t\'a été attribuée.');
        }

        return back()->with('status', $available ? 'Tu es maintenant disponible.' : 'Tu es maintenant indisponible.');
    }

    public function show(Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        return view('backoffice.listening.show', [
            'conversation' => $conversation->load(['member', 'listener', 'alerts']),
            'messages' => $conversation->messages()->with('sender')->oldest('id')->get(),
        ]);
    }

    public function take(Request $request, Conversation $conversation, ListenerQueue $queue): RedirectResponse
    {
        $this->authorize('take', $conversation);

        $conversation = $queue->assign($conversation, $request->user());

        if ($conversation->listener_id !== $request->user()->id) {
            return back()->with('error', 'Cette conversation vient d\'être prise par un autre écoutant.');
        }

        return redirect()->route('backoffice.listening.show', $conversation);
    }

    public function send(SendMessageRequest $request, Conversation $conversation, SendMessage $send): RedirectResponse
    {
        $this->authorize('participate', $conversation);

        $send($conversation, $request->user(), $request->validated('body'));

        return redirect()->route('backoffice.listening.show', $conversation);
    }

    public function close(Conversation $conversation, ListenerQueue $queue): RedirectResponse
    {
        $this->authorize('participate', $conversation);

        if ($conversation->isOpen()) {
            $queue->close($conversation, 'listener');
        }

        return redirect()->route('backoffice.listening.index')->with('status', 'Conversation clôturée.');
    }
}
