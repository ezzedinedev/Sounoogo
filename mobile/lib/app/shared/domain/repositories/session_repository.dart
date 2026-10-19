import '../entities/user.dart';

/// Session du membre sur cet appareil.
abstract interface class SessionRepository {
  /// Vrai si un jeton est conservé sur l'appareil.
  Future<bool> hasToken();

  /// Profil du membre connecté, lu sur le serveur.
  Future<User> currentUser();

  Future<void> save(String token);

  /// Révoque le jeton côté serveur si [remote] (au mieux), puis l'efface de l'appareil.
  Future<void> clear({bool remote = true});
}
