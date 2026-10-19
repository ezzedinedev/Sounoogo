import '../entities/privacy_settings.dart';
import '../repositories/app_lock_repository.dart';
import '../repositories/settings_repository.dart';

/// Applique les règles du mode discret : sans code, pas de verrou, ni d'empreinte, ni de sortie rapide
/// (la calculatrice se déverrouille avec le code).
final class UpdatePrivacySettings {
  const UpdatePrivacySettings(this._settings, this._lock);

  final SettingsRepository _settings;
  final AppLockRepository _lock;

  Future<PrivacySettings> call(PrivacySettings requested) async {
    final hasPin = await _lock.hasPin();
    final canUseBiometrics = hasPin && await _lock.canUseBiometrics();
    final settings = requested.copyWith(
      lockEnabled: hasPin && requested.lockEnabled,
      biometricEnabled: canUseBiometrics && requested.lockEnabled && requested.biometricEnabled,
      quickExitEnabled: hasPin && requested.quickExitEnabled,
    );
    await _settings.savePrivacy(settings);
    return settings;
  }
}
