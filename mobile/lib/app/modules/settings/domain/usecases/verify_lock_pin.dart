import '../entities/pin_check.dart';
import '../repositories/app_lock_repository.dart';

final class VerifyLockPin {
  const VerifyLockPin(this._repository);

  final AppLockRepository _repository;

  Future<PinCheck> call(String pin) => _repository.verifyPin(pin);
}
