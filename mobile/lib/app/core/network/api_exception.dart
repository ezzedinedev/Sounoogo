/// Erreur HTTP normalisée par [ApiProvider]. Ne quitte jamais la couche données :
/// les repositories la traduisent en `Failure` (voir `guardApi`).
class ApiException implements Exception {
  const ApiException(this.message, {this.statusCode, this.fieldErrors = const {}});

  final String message;

  /// Absent quand la requête n'a pas atteint le serveur.
  final int? statusCode;
  final Map<String, List<String>> fieldErrors;

  bool get isUnauthorized => statusCode == 401;

  @override
  String toString() => message;
}
