import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:sunoogo/app/core/error/failure.dart';
import 'package:sunoogo/app/shared/domain/entities/user.dart';
import 'package:sunoogo/app/shared/domain/repositories/reference_repository.dart';
import 'package:sunoogo/app/shared/domain/repositories/session_repository.dart';
import 'package:sunoogo/app/shared/domain/usecases/load_reference_data.dart';
import 'package:sunoogo/app/shared/domain/usecases/restore_session.dart';

class _MockSessionRepository extends Mock implements SessionRepository {}

class _MockReferenceRepository extends Mock implements ReferenceRepository {}

void main() {
  late _MockSessionRepository sessions;
  const member = User(id: 1, pseudonym: 'Baobab-204', role: 'member', isAnonymous: true, country: 'BF');

  setUp(() => sessions = _MockSessionRepository());

  test("sans jeton, on ne contacte pas l'API", () async {
    when(sessions.hasToken).thenAnswer((_) async => false);

    expect(await RestoreSession(sessions)(), isNull);
    verifyNever(sessions.currentUser);
  });

  test('avec un jeton valide, le membre est restauré', () async {
    when(sessions.hasToken).thenAnswer((_) async => true);
    when(sessions.currentUser).thenAnswer((_) async => member);

    expect(await RestoreSession(sessions)(), member);
  });

  test("un jeton refusé ou l'absence de réseau renvoie vers l'accueil", () async {
    when(sessions.hasToken).thenAnswer((_) async => true);
    when(sessions.currentUser).thenThrow(const UnauthorizedFailure());

    expect(await RestoreSession(sessions)(), isNull);
  });

  test("hors ligne, les numéros d'urgence par défaut restent disponibles", () async {
    final references = _MockReferenceRepository();
    when(references.load).thenThrow(const NetworkFailure());

    final data = await LoadReferenceData(references)();

    expect(data.isFallback, isTrue);
    expect(data.emergencyContacts.map((c) => c.phone), containsAll(['17', '18']));
  });
}
