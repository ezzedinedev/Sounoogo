import '../../../../shared/domain/entities/user.dart';
import '../repositories/listening_repository.dart';

/// Le membre souhaite retrouver l'écoutant de cette conversation.
final class PreferListener {
  const PreferListener(this._repository);

  final ListeningRepository _repository;

  Future<User> call(int conversationId) => _repository.preferListenerOf(conversationId);

  Future<User> forget() => _repository.forgetPreferredListener();
}
