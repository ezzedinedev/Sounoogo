import '../../../../shared/data/models/user_model.dart';
import '../../domain/entities/auth_session.dart';

/// Réponse des routes `/auth/*` : `{ token, user, recovery_code? }`.
abstract final class AuthSessionModel {
  static AuthSession fromJson(Map<String, dynamic> json) => AuthSession(
    token: json['token'] as String,
    user: UserModel.fromJson(json['user'] as Map<String, dynamic>),
    recoveryCode: json['recovery_code'] as String?,
  );
}
