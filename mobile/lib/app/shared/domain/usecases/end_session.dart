import '../repositories/session_repository.dart';

final class EndSession {
  const EndSession(this._repository);

  final SessionRepository _repository;

  Future<void> call({bool remote = true}) => _repository.clear(remote: remote);
}
