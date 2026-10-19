import '../entities/pin_check.dart';

abstract interface class AppLockRepository {
  Future<bool> hasPin();

  /// Nombre de chiffres du code (pour savoir quand le vérifier), ou `null` sans code.
  Future<int?> pinLength();

  Future<void> savePin(String pin);

  Future<PinCheck> verifyPin(String pin);

  Future<void> clearPin();

  /// Le téléphone propose l'empreinte ou la reconnaissance faciale.
  Future<bool> canUseBiometrics();

  Future<bool> authenticateWithBiometrics(String reason);
}
