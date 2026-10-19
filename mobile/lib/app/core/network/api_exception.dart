/// Erreur d'API normalisée, avec un message lisible par le membre.
class ApiException implements Exception {
  const ApiException(this.message, {this.statusCode, this.fieldErrors = const {}});

  final String message;
  final int? statusCode;
  final Map<String, List<String>> fieldErrors;

  bool get isUnauthorized => statusCode == 401;
  bool get isPaymentRequired => statusCode == 402;
  bool get isNetwork => statusCode == null;

  String? firstFieldError(String field) => fieldErrors[field]?.firstOrNull;

  @override
  String toString() => message;
}
