import '../entities/conversation.dart';
import '../repositories/listening_repository.dart';

/// Demande un écoutant, éventuellement sur un thème choisi.
final class OpenConversation {
  const OpenConversation(this._repository);

  final ListeningRepository _repository;

  Future<Conversation> call({String? topic}) => _repository.open(topic: topic);
}
