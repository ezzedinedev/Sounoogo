import '../entities/display_settings.dart';
import '../entities/privacy_settings.dart';

abstract interface class SettingsRepository {
  Future<PrivacySettings> privacy();

  /// Enregistre et applique les réglages (masquage dans les apps récentes, par exemple).
  Future<void> savePrivacy(PrivacySettings settings);

  Future<DisplaySettings> display();

  Future<void> saveDisplay(DisplaySettings settings);
}
