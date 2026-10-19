import 'package:shared_preferences/shared_preferences.dart';

import '../../../../core/storage/secure_cache.dart';
import '../../domain/entities/display_settings.dart';
import '../../domain/entities/privacy_settings.dart';

/// Mode discret : stockage chiffré, effacé à la déconnexion.
/// Affichage : préférences simples, conservées pour la personne suivante sur l'appareil.
class SettingsLocalDataSource {
  SettingsLocalDataSource(this._cache, this._preferences);

  static const _privacyKey = 'privacy_settings';
  static const _textSizeKey = 'sunoogo.text_size';

  final SecureCache _cache;
  final SharedPreferencesAsync _preferences;

  Future<PrivacySettings> privacy() async {
    final json = await _cache.read(_privacyKey) ?? const {};
    return PrivacySettings(
      lockEnabled: json['lock_enabled'] as bool? ?? false,
      biometricEnabled: json['biometric_enabled'] as bool? ?? false,
      lockDelay: LockDelay.fromName(json['lock_delay'] as String?),
      hideInRecents: json['hide_in_recents'] as bool? ?? false,
      quickExitEnabled: json['quick_exit_enabled'] as bool? ?? false,
    );
  }

  Future<void> savePrivacy(PrivacySettings settings) => _cache.write(_privacyKey, {
    'lock_enabled': settings.lockEnabled,
    'biometric_enabled': settings.biometricEnabled,
    'lock_delay': settings.lockDelay.name,
    'hide_in_recents': settings.hideInRecents,
    'quick_exit_enabled': settings.quickExitEnabled,
  });

  Future<DisplaySettings> display() async => DisplaySettings(textSize: TextSize.fromName(await _preferences.getString(_textSizeKey)));

  Future<void> saveDisplay(DisplaySettings settings) => _preferences.setString(_textSizeKey, settings.textSize.name);
}
