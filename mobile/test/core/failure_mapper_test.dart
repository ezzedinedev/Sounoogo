import 'package:flutter_test/flutter_test.dart';
import 'package:sunoogo/app/core/error/failure.dart';
import 'package:sunoogo/app/core/error/failure_mapper.dart';
import 'package:sunoogo/app/core/network/api_exception.dart';

void main() {
  test('chaque statut HTTP devient un échec typé', () {
    expect(const ApiException('hors ligne').toFailure(), isA<NetworkFailure>());
    expect(const ApiException('expiré', statusCode: 401).toFailure(), isA<UnauthorizedFailure>());
    expect(const ApiException('payant', statusCode: 402).toFailure(), isA<PaymentRequiredFailure>());
    expect(const ApiException('interdit', statusCode: 403).toFailure(), isA<ServerFailure>());
  });

  test('les erreurs de validation gardent le détail par champ', () {
    final failure = const ApiException(
      'invalide',
      statusCode: 422,
      fieldErrors: {
        'email': ['Adresse déjà utilisée.'],
      },
    ).toFailure();

    expect(failure, isA<ValidationFailure>());
    expect((failure as ValidationFailure).firstError('email'), 'Adresse déjà utilisée.');
  });

  test('guardApi laisse passer le résultat et traduit les exceptions', () async {
    expect(await guardApi(() async => 42), 42);
    await expectLater(guardApi<void>(() async => throw const ApiException('panne', statusCode: 503)), throwsA(isA<ServerFailure>()));
  });
}
