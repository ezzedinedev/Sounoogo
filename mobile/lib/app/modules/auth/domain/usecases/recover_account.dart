import '../entities/auth_session.dart';
import '../repositories/auth_repository.dart';

/// Retrouver un espace anonyme sur un autre téléphone, avec le pseudonyme et le code de récupération.
final class RecoverAccount {
  const RecoverAccount(this._repository);

  final AuthRepository _repository;

  Future<AuthSession> call({required String pseudonym, required String recoveryCode}) =>
      _repository.recover(pseudonym: pseudonym.trim(), recoveryCode: recoveryCode.trim().toUpperCase());
}
