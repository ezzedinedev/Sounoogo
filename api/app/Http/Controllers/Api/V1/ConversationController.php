<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Listening\Actions\ReportConversation;
use App\Domain\Listening\Actions\SendMessage;
use App\Domain\Listening\Enums\RiskLevel;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;
use App\Domain\Listening\Services\EmergencyService;
use App\Domain\Listening\Services\ListenerQueue;
use App\Http\Controllers\Controller;
use App\Http\Requests\Listening\OpenConversationRequest;
use App\Http\Requests\Listening\RateConversationRequest;
use App\Http\Requests\Listening\ReportRequest;
use App\Http\Requests\Listening\SendMessageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\EmergencyContactResource;
use App\Http\Resources\MessageResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Écoute anonyme
 */
class ConversationController extends Controller
{
    /** Mes conversations (membre ou écoutant). */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $conversations = Conversation::query()
            ->where(fn ($q) => $q->where('member_id', $user->id)->orWhere('listener_id', $user->id))
            ->with(['member', 'listener', 'latestMessage.sender'])
            ->latest('updated_at')
            ->paginate(20);

        return ConversationResource::collection($conversations);
    }

    /** Démarrer une conversation d'écoute, ou reprendre celle en cours. */
    public function store(OpenConversationRequest $request, ListenerQueue $queue): ConversationResource
    {
        $conversation = $queue->open(
            $request->user(),
            $request->validated('topic'),
            $request->validated('channel') ?? 'chat',
        );

        return new ConversationResource($conversation->load(['member', 'listener']));
    }

    public function show(Conversation $conversation): ConversationResource
    {
        $this->authorize('view', $conversation);

        return new ConversationResource($conversation->load(['member', 'listener']));
    }

    /** Messages, du plus récent au plus ancien (pagination par curseur). */
    public function messages(Conversation $conversation): AnonymousResourceCollection
    {
        $this->authorize('view', $conversation);

        return MessageResource::collection(
            $conversation->messages()->with('sender')->latest('id')->cursorPaginate(50)
        );
    }

    /**
     * Envoyer un message.
     *
     * Si le message d'un membre contient des signaux de détresse, la réponse inclut
     * les numéros d'urgence à afficher et un superviseur est alerté en temps réel.
     */
    public function send(SendMessageRequest $request, Conversation $conversation, SendMessage $send, EmergencyService $emergency): JsonResponse
    {
        $this->authorize('participate', $conversation);

        [$message, $risk] = $send($conversation, $request->user(), $request->validated('body'));

        return response()->json([
            'message' => new MessageResource($message->load('sender')),
            'risk_level' => $risk->value,
            'emergency_contacts' => $risk === RiskLevel::High
                ? EmergencyContactResource::collection($emergency->contactsFor($request->user()->country))
                : [],
        ], 201);
    }

    public function close(Request $request, Conversation $conversation, ListenerQueue $queue): ConversationResource
    {
        $this->authorize('participate', $conversation);
        abort_unless($conversation->isOpen(), 409, 'Cette conversation est déjà terminée.');

        $reason = $request->user()->id === $conversation->member_id ? 'member' : 'listener';

        return new ConversationResource($queue->close($conversation, $reason)->load(['member', 'listener']));
    }

    /** Évaluer l'écoute reçue (1 à 5). */
    public function rate(RateConversationRequest $request, Conversation $conversation): ConversationResource
    {
        $this->authorize('rate', $conversation);
        abort_if($conversation->isOpen(), 409, 'La conversation doit être terminée pour être évaluée.');

        $conversation->update(['member_rating' => $request->validated('rating')]);

        return new ConversationResource($conversation->load(['member', 'listener']));
    }

    /** Signaler un comportement inapproprié dans la conversation. */
    public function report(ReportRequest $request, Conversation $conversation, ReportConversation $report): JsonResponse
    {
        $this->authorize('participate', $conversation);

        $report(
            $request->user(),
            $conversation,
            $request->validated('reason'),
            $request->validated('details'),
            $request->validated('message_id') ? Message::find($request->validated('message_id')) : null,
        );

        return response()->json(['message' => 'Merci, ton signalement a été transmis à l\'équipe de modération.'], 201);
    }
}
