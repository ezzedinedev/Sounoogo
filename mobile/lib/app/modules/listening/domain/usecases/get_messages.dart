import '../entities/message.dart';
import '../repositories/listening_repository.dart';

final class GetMessages {
  const GetMessages(this._repository);

  final ListeningRepository _repository;

  Future<List<Message>> call(int conversationId) => _repository.messages(conversationId);
}
