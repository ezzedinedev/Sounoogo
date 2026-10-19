import '../entities/privacy_settings.dart';
import '../repositories/app_lock_repository.dart';
import '../repositories/settings_repository.dart';

/// Supprime le code et tout ce qui en dépend (verrou, empreinte, sortie rapide).
final class DisableLock {
  const DisableLock(this._settings, this._lock);

  final SettingsRepository _settings;
  final AppLockRepository _lock;

  Future<PrivacySettings> call() async {
    await _lock.clearPin();
    final current = await _settings.privacy();
    final settings = current.copyWith(lockEnabled: false, biometricEnabled: false, quickExitEnabled: false);
    await _settings.savePrivacy(settings);
    return settings;
  }
}
