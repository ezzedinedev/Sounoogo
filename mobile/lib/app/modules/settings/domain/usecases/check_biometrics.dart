import '../repositories/app_lock_repository.dart';

/// Le téléphone permet-il le déverrouillage par empreinte ou visage ?
final class CheckBiometrics {
  const CheckBiometrics(this._repository);

  final AppLockRepository _repository;

  Future<bool> call() => _repository.canUseBiometrics();
}
