import '../repositories/listening_repository.dart';

final class RateConversation {
  const RateConversation(this._repository);

  final ListeningRepository _repository;

  Future<void> call(int conversationId, int rating) => _repository.rate(conversationId, rating.clamp(1, 5));
}
