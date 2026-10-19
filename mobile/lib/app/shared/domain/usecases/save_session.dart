import '../repositories/session_repository.dart';

final class SaveSession {
  const SaveSession(this._repository);

  final SessionRepository _repository;

  Future<void> call(String token) => _repository.save(token);
}
