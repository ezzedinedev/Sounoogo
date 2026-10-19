import '../../domain/entities/pin_check.dart';
import '../../domain/repositories/app_lock_repository.dart';
import '../datasources/device_security_data_source.dart';
import '../datasources/pin_local_data_source.dart';

class AppLockRepositoryImpl implements AppLockRepository {
  AppLockRepositoryImpl(this._pins, this._device, {DateTime Function()? clock}) : _now = clock ?? DateTime.now;

  /// Essais autorisés avant un blocage, qui double à chaque nouvelle série d'échecs.
  static const attemptsPerRound = 5;
  static const _firstLockout = Duration(seconds: 30);

  final PinLocalDataSource _pins;
  final DeviceSecurityDataSource _device;
  final DateTime Function() _now;

  @override
  Future<bool> hasPin() async => await _pins.read() != null;

  @override
  Future<int?> pinLength() async => (await _pins.read())?.length;

  @override
  Future<void> savePin(String pin) async {
    final salt = _pins.hasher.newSalt();
    await _pins.write(PinRecord(salt: salt, hash: await _pins.hasher.hash(pin, salt), length: pin.length));
  }

  @override
  Future<PinCheck> verifyPin(String pin) async {
    final record = await _pins.read();
    if (record == null) {
      return const PinAccepted();
    }
    final lockedUntil = record.lockedUntil;
    if (lockedUntil != null && _now().isBefore(lockedUntil)) {
      return PinLockedOut(lockedUntil);
    }

    if (await _pins.hasher.matches(pin, record.salt, record.hash)) {
      await _pins.write(PinRecord(salt: record.salt, hash: record.hash, length: record.length));
      return const PinAccepted();
    }

    final failed = record.failedAttempts + 1;
    if (failed % attemptsPerRound == 0) {
      final until = _now().add(_firstLockout * (1 << (failed ~/ attemptsPerRound - 1)));
      await _pins.write(PinRecord(salt: record.salt, hash: record.hash, length: record.length, failedAttempts: failed, lockedUntil: until));
      return PinLockedOut(until);
    }
    await _pins.write(PinRecord(salt: record.salt, hash: record.hash, length: record.length, failedAttempts: failed));
    return PinRejected(attemptsPerRound - failed % attemptsPerRound);
  }

  @override
  Future<void> clearPin() => _pins.clear();

  @override
  Future<bool> canUseBiometrics() => _device.canUseBiometrics();

  @override
  Future<bool> authenticateWithBiometrics(String reason) => _device.authenticate(reason);
}
