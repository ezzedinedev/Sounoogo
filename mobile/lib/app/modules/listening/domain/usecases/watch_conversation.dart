import '../entities/conversation_event.dart';
import '../repositories/listening_repository.dart';

final class WatchConversation {
  const WatchConversation(this._repository);

  final ListeningRepository _repository;

  Stream<ConversationEvent> call(int conversationId) => _repository.watch(conversationId);
}
