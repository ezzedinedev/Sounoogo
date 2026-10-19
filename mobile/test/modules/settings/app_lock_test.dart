import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:sunoogo/app/core/storage/secure_cache.dart';
import 'package:sunoogo/app/modules/settings/data/datasources/device_security_data_source.dart';
import 'package:sunoogo/app/modules/settings/data/datasources/pin_hasher.dart';
import 'package:sunoogo/app/modules/settings/data/datasources/pin_local_data_source.dart';
import 'package:sunoogo/app/modules/settings/data/repositories/app_lock_repository_impl.dart';
import 'package:sunoogo/app/modules/settings/domain/entities/pin_check.dart';
import 'package:sunoogo/app/modules/settings/domain/entities/privacy_settings.dart';
import 'package:sunoogo/app/modules/settings/domain/repositories/app_lock_repository.dart';
import 'package:sunoogo/app/modules/settings/domain/repositories/settings_repository.dart';
import 'package:sunoogo/app/modules/settings/domain/usecases/set_lock_pin.dart';
import 'package:sunoogo/app/modules/settings/domain/usecases/update_privacy_settings.dart';

/// Cache en mémoire à la place du stockage chiffré du téléphone.
class _MemoryCache extends Fake implements SecureCache {
  final _data = <String, Map<String, dynamic>>{};

  @override
  Future<Map<String, dynamic>?> read(String key) async => _data[key];

  @override
  Future<void> write(String key, Map<String, dynamic> value) async => _data[key] = value;

  @override
  Future<void> delete(String key) async => _data.remove(key);
}

class _MockDevice extends Mock implements DeviceSecurityDataSource {}

class _MockLockRepository extends Mock implements AppLockRepository {}

class _MockSettingsRepository extends Mock implements SettingsRepository {}

void main() {
  setUpAll(() => registerFallbackValue(const PrivacySettings()));

  group('AppLockRepositoryImpl', () {
    late DateTime now;
    late AppLockRepositoryImpl repository;

    setUp(() {
      now = DateTime(2026, 10, 20, 9);
      repository = AppLockRepositoryImpl(
        PinLocalDataSource(_MemoryCache(), hasher: const PinHasher(iterations: 10)),
        _MockDevice(),
        clock: () => now,
      );
    });

    test('accepte le bon code et refuse les autres', () async {
      await repository.savePin('2468');

      expect(await repository.pinLength(), 4);
      expect(await repository.verifyPin('2468'), isA<PinAccepted>());
      expect(await repository.verifyPin('1357'), isA<PinRejected>());
    });

    test('bloque après cinq essais, puis double le délai', () async {
      await repository.savePin('2468');
      for (var i = 0; i < 4; i++) {
        expect(await repository.verifyPin('0000'), isA<PinRejected>());
      }

      final first = await repository.verifyPin('0000');
      expect(first, isA<PinLockedOut>());
      expect((first as PinLockedOut).until, now.add(const Duration(seconds: 30)));
      expect(await repository.verifyPin('2468'), isA<PinLockedOut>(), reason: 'même le bon code attend la fin du blocage');

      now = now.add(const Duration(seconds: 31));
      for (var i = 0; i < 4; i++) {
        await repository.verifyPin('0000');
      }
      final second = await repository.verifyPin('0000') as PinLockedOut;
      expect(second.until, now.add(const Duration(minutes: 1)));
    });

    test('le bon code remet le compteur à zéro', () async {
      await repository.savePin('2468');
      for (var i = 0; i < 4; i++) {
        await repository.verifyPin('0000');
      }
      expect(await repository.verifyPin('2468'), isA<PinAccepted>());
      expect(await repository.verifyPin('0000'), isA<PinRejected>().having((r) => r.attemptsBeforeLockout, 'essais restants', 4));
    });
  });

  group('SetLockPin', () {
    late _MockLockRepository lock;

    setUp(() {
      lock = _MockLockRepository();
      when(() => lock.savePin(any())).thenAnswer((_) async {});
    });

    test('refuse les codes trop simples ou mal formés', () async {
      expect(await SetLockPin(lock)('12a4', confirmation: '12a4'), isNotNull);
      expect(await SetLockPin(lock)('1234', confirmation: '1234'), contains('trop facile'));
      expect(await SetLockPin(lock)('7777', confirmation: '7777'), contains('trop facile'));
      expect(await SetLockPin(lock)('2468', confirmation: '2469'), contains('ne correspondent pas'));
      verifyNever(() => lock.savePin(any()));
    });

    test('enregistre un code valide confirmé', () async {
      expect(await SetLockPin(lock)('2468', confirmation: '2468'), isNull);
      verify(() => lock.savePin('2468')).called(1);
    });
  });

  test('sans code, ni verrou ni sortie rapide ne peuvent être activés', () async {
    final lock = _MockLockRepository();
    final settings = _MockSettingsRepository();
    when(lock.hasPin).thenAnswer((_) async => false);
    when(lock.canUseBiometrics).thenAnswer((_) async => true);
    when(() => settings.savePrivacy(any())).thenAnswer((_) async {});

    final saved = await UpdatePrivacySettings(settings, lock)(
      const PrivacySettings(lockEnabled: true, biometricEnabled: true, quickExitEnabled: true, hideInRecents: true),
    );

    expect(saved.lockEnabled, isFalse);
    expect(saved.biometricEnabled, isFalse);
    expect(saved.quickExitEnabled, isFalse);
    expect(saved.hideInRecents, isTrue, reason: 'le masquage ne dépend pas du code');
  });
}
