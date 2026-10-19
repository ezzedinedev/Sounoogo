import 'package:get/get.dart';

import '../../../../core/widgets/app_toast.dart';
import '../../domain/entities/display_settings.dart';
import '../../domain/entities/privacy_settings.dart';
import '../../domain/usecases/check_biometrics.dart';
import '../../domain/usecases/disable_lock.dart';
import '../../domain/usecases/set_lock_pin.dart';
import '../../domain/usecases/update_privacy_settings.dart';
import '../services/app_lock_service.dart';
import '../services/display_service.dart';

/// Écran « Confidentialité et affichage ».
class SettingsController extends GetxController {
  SettingsController({
    required this._updatePrivacySettings,
    required this._setLockPin,
    required this._disableLock,
    required this._checkBiometrics,
  });

  final UpdatePrivacySettings _updatePrivacySettings;
  final SetLockPin _setLockPin;
  final DisableLock _disableLock;
  final CheckBiometrics _checkBiometrics;
  final AppLockService _lock = Get.find();
  final DisplayService _display = Get.find();

  final biometricsAvailable = false.obs;

  Rx<PrivacySettings> get privacy => _lock.settings;
  Rx<TextSize> get textSize => _display.textSize;
  bool get hasPin => _lock.pinLength.value != null;

  @override
  void onInit() {
    super.onInit();
    _checkBiometrics().then((available) => biometricsAvailable.value = available);
  }

  Future<void> updatePrivacy(PrivacySettings requested) async => _lock.apply(await _updatePrivacySettings(requested));

  /// Renvoie un message d'erreur, ou `null` si le code est enregistré et le verrou activé.
  Future<String?> createPin(String pin, String confirmation) async {
    final error = await _setLockPin(pin, confirmation: confirmation);
    if (error == null) {
      await updatePrivacy(privacy.value.copyWith(lockEnabled: true));
    }
    return error;
  }

  Future<void> disableLock() async {
    await _lock.apply(await _disableLock());
    AppToast.show('Verrou désactivé', 'Ton code a été supprimé de ce téléphone.');
  }

  Future<void> setTextSize(TextSize size) => _display.setTextSize(size);
}
