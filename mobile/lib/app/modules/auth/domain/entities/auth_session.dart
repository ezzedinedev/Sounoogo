import '../../../../shared/domain/entities/user.dart';

/// Résultat d'une authentification : jeton, profil et, pour un accès anonyme,
/// le code de récupération à montrer une seule fois.
class AuthSession {
  const AuthSession({required this.token, required this.user, this.recoveryCode});

  final String token;
  final User user;
  final String? recoveryCode;

  String get pseudonym => user.pseudonym;
}
