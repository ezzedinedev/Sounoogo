import '../entities/auth_session.dart';

abstract interface class AuthRepository {
  /// Crée un compte anonyme : pseudonyme et code de récupération générés par le serveur.
  Future<AuthSession> startAnonymous();

  Future<AuthSession> recover({required String pseudonym, required String recoveryCode});

  /// [login] est un e-mail ou un numéro de téléphone.
  Future<AuthSession> login({required String login, required String password});
}
