import '../entities/conversation.dart';
import '../repositories/listening_repository.dart';

final class GetConversation {
  const GetConversation(this._repository);

  final ListeningRepository _repository;

  Future<Conversation> call(int conversationId) => _repository.find(conversationId);
}
