import '../../domain/entities/display_settings.dart';
import '../../domain/entities/privacy_settings.dart';
import '../../domain/repositories/settings_repository.dart';
import '../datasources/device_security_data_source.dart';
import '../datasources/settings_local_data_source.dart';

class SettingsRepositoryImpl implements SettingsRepository {
  SettingsRepositoryImpl(this._local, this._device);

  final SettingsLocalDataSource _local;
  final DeviceSecurityDataSource _device;

  /// Lit les réglages et réapplique la protection d'écran (utile au démarrage).
  @override
  Future<PrivacySettings> privacy() async {
    final settings = await _local.privacy();
    await _device.setSecureScreen(settings.hideInRecents);
    return settings;
  }

  @override
  Future<void> savePrivacy(PrivacySettings settings) async {
    await _local.savePrivacy(settings);
    await _device.setSecureScreen(settings.hideInRecents);
  }

  @override
  Future<DisplaySettings> display() => _local.display();

  @override
  Future<void> saveDisplay(DisplaySettings settings) => _local.saveDisplay(settings);
}
