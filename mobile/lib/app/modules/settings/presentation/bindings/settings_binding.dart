import 'package:get/get.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../../data/datasources/device_security_data_source.dart';
import '../../data/datasources/pin_local_data_source.dart';
import '../../data/datasources/settings_local_data_source.dart';
import '../../data/repositories/app_lock_repository_impl.dart';
import '../../data/repositories/settings_repository_impl.dart';
import '../../domain/repositories/app_lock_repository.dart';
import '../../domain/repositories/settings_repository.dart';
import '../../domain/usecases/check_biometrics.dart';
import '../../domain/usecases/disable_lock.dart';
import '../../domain/usecases/get_display_settings.dart';
import '../../domain/usecases/get_pin_length.dart';
import '../../domain/usecases/get_privacy_settings.dart';
import '../../domain/usecases/set_lock_pin.dart';
import '../../domain/usecases/unlock_with_biometrics.dart';
import '../../domain/usecases/update_display_settings.dart';
import '../../domain/usecases/update_privacy_settings.dart';
import '../../domain/usecases/verify_lock_pin.dart';
import '../controllers/settings_controller.dart';
import '../services/app_lock_service.dart';
import '../services/display_service.dart';

/// Global : le verrou et la taille du texte s'appliquent à toute l'app dès le démarrage.
class SettingsBinding extends Bindings {
  @override
  void dependencies() {
    final device = DeviceSecurityDataSource();
    final SettingsRepository settings = SettingsRepositoryImpl(SettingsLocalDataSource(Get.find(), SharedPreferencesAsync()), device);
    final AppLockRepository lock = AppLockRepositoryImpl(PinLocalDataSource(Get.find()), device);

    Get
      ..put(settings, permanent: true)
      ..put(lock, permanent: true)
      ..put(
        AppLockService(
          getPrivacySettings: GetPrivacySettings(settings),
          getPinLength: GetPinLength(lock),
          verifyLockPin: VerifyLockPin(lock),
          unlockWithBiometrics: UnlockWithBiometrics(lock),
        ),
        permanent: true,
      )
      ..put(
        DisplayService(getDisplaySettings: GetDisplaySettings(settings), updateDisplaySettings: UpdateDisplaySettings(settings)),
        permanent: true,
      );
  }
}

class SettingsScreenBinding extends Bindings {
  @override
  void dependencies() {
    final SettingsRepository settings = Get.find();
    final AppLockRepository lock = Get.find();
    Get.lazyPut(
      () => SettingsController(
        updatePrivacySettings: UpdatePrivacySettings(settings, lock),
        setLockPin: SetLockPin(lock),
        disableLock: DisableLock(settings, lock),
        checkBiometrics: CheckBiometrics(lock),
      ),
    );
  }
}
