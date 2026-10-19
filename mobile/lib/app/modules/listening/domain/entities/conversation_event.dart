import 'conversation.dart';
import 'message.dart';

/// Événement reçu en temps réel sur une conversation.
sealed class ConversationEvent {
  const ConversationEvent();
}

final class MessageReceived extends ConversationEvent {
  const MessageReceived(this.message);

  final Message message;
}

final class ConversationUpdated extends ConversationEvent {
  const ConversationUpdated(this.conversation);

  final Conversation conversation;
}
