import '../entities/auth_session.dart';
import '../repositories/auth_repository.dart';

/// Parler tout de suite, sans aucune donnée personnelle.
final class StartAnonymousSession {
  const StartAnonymousSession(this._repository);

  final AuthRepository _repository;

  Future<AuthSession> call() => _repository.startAnonymous();
}
