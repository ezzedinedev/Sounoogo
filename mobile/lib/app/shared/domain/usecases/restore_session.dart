import '../../../core/error/failure.dart';
import '../entities/user.dart';
import '../repositories/session_repository.dart';

/// Au démarrage : retrouve le membre si un jeton valide est conservé.
/// Renvoie `null` s'il faut repasser par l'accueil (aucun jeton, jeton expiré, pas de réseau).
final class RestoreSession {
  const RestoreSession(this._repository);

  final SessionRepository _repository;

  Future<User?> call() async {
    if (!await _repository.hasToken()) {
      return null;
    }
    try {
      return await _repository.currentUser();
    } on Failure {
      return null;
    }
  }
}
