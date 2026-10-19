import '../entities/conversation.dart';
import '../repositories/listening_repository.dart';

final class GetActiveConversation {
  const GetActiveConversation(this._repository);

  final ListeningRepository _repository;

  Future<Conversation?> call() => _repository.active();
}
