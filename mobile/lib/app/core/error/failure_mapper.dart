import '../network/api_exception.dart';
import 'failure.dart';

/// Exécute un appel de la couche données et traduit les erreurs techniques en [Failure].
/// À utiliser dans chaque implémentation de repository.
Future<T> guardApi<T>(Future<T> Function() call) async {
  try {
    return await call();
  } on ApiException catch (error) {
    throw error.toFailure();
  }
}

extension ApiExceptionToFailure on ApiException {
  Failure toFailure() => switch (statusCode) {
    null => NetworkFailure(message),
    401 => UnauthorizedFailure(message),
    402 => PaymentRequiredFailure(message),
    422 => ValidationFailure(message, fieldErrors: fieldErrors),
    final code => ServerFailure(message, statusCode: code),
  };
}
