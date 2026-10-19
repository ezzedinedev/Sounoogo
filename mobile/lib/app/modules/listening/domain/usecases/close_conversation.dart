import '../entities/conversation.dart';
import '../repositories/listening_repository.dart';

final class CloseConversation {
  const CloseConversation(this._repository);

  final ListeningRepository _repository;

  Future<Conversation> call(int conversationId) => _repository.close(conversationId);
}
