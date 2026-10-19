import '../../../../shared/domain/entities/user.dart';

abstract interface class ProfileRepository {
  /// Rattache un e-mail ou un téléphone et un mot de passe à un compte anonyme.
  Future<User> secureAccount({String? email, String? phone, required String password});

  /// Supprime définitivement le compte et ses données.
  Future<void> deleteAccount();
}
