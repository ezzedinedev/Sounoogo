import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:sunoogo/app/core/error/failure.dart';
import 'package:sunoogo/app/core/network/api_exception.dart';
import 'package:sunoogo/app/modules/safety_plan/data/datasources/safety_plan_local_data_source.dart';
import 'package:sunoogo/app/modules/safety_plan/data/datasources/safety_plan_remote_data_source.dart';
import 'package:sunoogo/app/modules/safety_plan/data/repositories/safety_plan_repository_impl.dart';
import 'package:sunoogo/app/modules/safety_plan/domain/entities/safety_plan.dart';
import 'package:sunoogo/app/modules/safety_plan/domain/repositories/safety_plan_repository.dart';
import 'package:sunoogo/app/modules/safety_plan/domain/usecases/save_safety_plan.dart';

class _MockRemote extends Mock implements SafetyPlanRemoteDataSource {}

class _MockLocal extends Mock implements SafetyPlanLocalDataSource {}

class _MockRepository extends Mock implements SafetyPlanRepository {}

void main() {
  const plan = SafetyPlan(
    reasonsToLive: ['Mon petit frère'],
    supportContacts: [SafetyContact(name: 'Awa', phone: '70123456')],
  );

  setUpAll(() => registerFallbackValue(const SafetyPlan()));

  group('SafetyPlanRepositoryImpl', () {
    late _MockRemote remote;
    late _MockLocal local;
    late SafetyPlanRepositoryImpl repository;

    setUp(() {
      remote = _MockRemote();
      local = _MockLocal();
      repository = SafetyPlanRepositoryImpl(remote, local);
      when(() => local.write(any())).thenAnswer((_) async {});
      when(local.clear).thenAnswer((_) async {});
    });

    test('garde une copie locale du plan reçu', () async {
      when(remote.get).thenAnswer((_) async => plan);

      expect(await repository.get(), plan);
      verify(() => local.write(plan)).called(1);
    });

    test('hors ligne, le plan reste accessible depuis la copie locale', () async {
      when(remote.get).thenThrow(const ApiException('hors ligne'));
      when(local.read).thenAnswer((_) async => plan);

      expect(await repository.get(), plan);
    });

    test('hors ligne et sans copie, l\'échec réseau remonte', () async {
      when(remote.get).thenThrow(const ApiException('hors ligne'));
      when(local.read).thenAnswer((_) async => null);

      await expectLater(repository.get(), throwsA(isA<NetworkFailure>()));
    });

    test('un plan supprimé sur le serveur disparaît aussi du téléphone', () async {
      when(remote.get).thenAnswer((_) async => null);

      expect(await repository.get(), isNull);
      verify(local.clear).called(1);
    });
  });

  test('SaveSafetyPlan retire les lignes et contacts vides', () async {
    final repository = _MockRepository();
    when(() => repository.save(any())).thenAnswer((invocation) async => invocation.positionalArguments.first as SafetyPlan);

    final saved = await SaveSafetyPlan(repository)(
      const SafetyPlan(
        warningSigns: ['  Je ne dors plus ', '', '   '],
        supportContacts: [
          SafetyContact(name: 'Awa', phone: '70 12 34 56'),
          SafetyContact(name: '', phone: ''),
        ],
      ),
    );

    expect(saved.warningSigns, ['Je ne dors plus']);
    expect(saved.supportContacts.single.phone, '70123456');
  });
}
