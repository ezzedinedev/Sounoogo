import '../repositories/app_lock_repository.dart';

final class UnlockWithBiometrics {
  const UnlockWithBiometrics(this._repository);

  final AppLockRepository _repository;

  Future<bool> call() async =>
      await _repository.canUseBiometrics() && await _repository.authenticateWithBiometrics('Déverrouiller l\'application');
}
