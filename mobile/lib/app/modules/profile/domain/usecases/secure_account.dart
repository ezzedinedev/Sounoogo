import '../../../../shared/domain/entities/user.dart';
import '../repositories/profile_repository.dart';

/// Sécurise un compte anonyme avec un identifiant (e-mail ou téléphone) et un mot de passe.
final class SecureAccount {
  const SecureAccount(this._repository);

  final ProfileRepository _repository;

  Future<User> call({required String login, required String password}) {
    final identifier = login.trim();
    final isEmail = identifier.contains('@');
    return _repository.secureAccount(
      email: isEmail ? identifier : null,
      phone: isEmail ? null : identifier.replaceAll(RegExp(r'[\s.-]'), ''),
      password: password,
    );
  }
}
