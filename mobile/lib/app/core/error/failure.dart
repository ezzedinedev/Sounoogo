/// Échec exposé aux couches domaine et présentation.
///
/// La couche données traduit ses exceptions techniques (HTTP, stockage, temps réel)
/// en [Failure] : le reste de l'app ne connaît ni Dio ni les codes HTTP.
sealed class Failure implements Exception {
  const Failure(this.message);

  /// Message prêt à être affiché au membre, en français.
  final String message;

  @override
  String toString() => message;
}

/// Pas de réseau, ou serveur injoignable.
final class NetworkFailure extends Failure {
  const NetworkFailure([super.message = 'Connexion impossible. Vérifie ta connexion internet et réessaie.']);
}

/// Session expirée ou compte suspendu.
final class UnauthorizedFailure extends Failure {
  const UnauthorizedFailure([super.message = 'Ta session a expiré. Reconnecte-toi pour continuer.']);
}

/// L'action demande un paiement (formation payante, par exemple).
final class PaymentRequiredFailure extends Failure {
  const PaymentRequiredFailure(super.message);
}

/// Données refusées, avec le détail par champ.
final class ValidationFailure extends Failure {
  const ValidationFailure(super.message, {this.fieldErrors = const {}});

  final Map<String, List<String>> fieldErrors;

  String? firstError(String field) => fieldErrors[field]?.firstOrNull;
}

/// Toute autre réponse refusée par le serveur (accès interdit, ressource introuvable, conflit, panne…).
final class ServerFailure extends Failure {
  const ServerFailure(super.message, {this.statusCode});

  final int? statusCode;
}
