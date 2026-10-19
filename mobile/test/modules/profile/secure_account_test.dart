import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:sunoogo/app/modules/profile/domain/repositories/profile_repository.dart';
import 'package:sunoogo/app/modules/profile/domain/usecases/secure_account.dart';
import 'package:sunoogo/app/shared/domain/entities/user.dart';

class _MockProfileRepository extends Mock implements ProfileRepository {}

void main() {
  late _MockProfileRepository repository;
  const member = User(id: 1, pseudonym: 'Karité-88', role: 'member', isAnonymous: false, country: 'BF');

  setUp(() {
    repository = _MockProfileRepository();
    when(
      () => repository.secureAccount(
        email: any(named: 'email'),
        phone: any(named: 'phone'),
        password: any(named: 'password'),
      ),
    ).thenAnswer((_) async => member);
  });

  test('un identifiant avec @ est un e-mail', () async {
    await SecureAccount(repository)(login: ' awa@exemple.bf ', password: 'secret123');

    verify(() => repository.secureAccount(email: 'awa@exemple.bf', password: 'secret123')).called(1);
  });

  test('sinon, c\'est un numéro de téléphone, sans espaces ni séparateurs', () async {
    await SecureAccount(repository)(login: '+226 70 12.34-56', password: 'secret123');

    verify(() => repository.secureAccount(phone: '+22670123456', password: 'secret123')).called(1);
  });
}
