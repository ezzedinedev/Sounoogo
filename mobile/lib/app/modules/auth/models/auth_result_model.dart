/// Réponse des routes d'authentification : jeton, profil, et code de récupération (accès anonyme).
class AuthResultModel {
  const AuthResultModel({required this.token, required this.user, this.recoveryCode});

  factory AuthResultModel.fromJson(Map<String, dynamic> json) => AuthResultModel(
        token: json['token'] as String,
        user: json['user'] as Map<String, dynamic>,
        recoveryCode: json['recovery_code'] as String?,
      );

  final String token;
  final Map<String, dynamic> user;
  final String? recoveryCode;

  String get pseudonym => user['pseudonym'] as String;
}
