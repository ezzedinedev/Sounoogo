import 'package:flutter/widgets.dart';
import 'package:get/get.dart';

import '../../../../shared/presentation/session_service.dart';
import '../../domain/entities/pin_check.dart';
import '../../domain/entities/privacy_settings.dart';
import '../../domain/usecases/get_pin_length.dart';
import '../../domain/usecases/get_privacy_settings.dart';
import '../../domain/usecases/unlock_with_biometrics.dart';
import '../../domain/usecases/verify_lock_pin.dart';

/// Mode discret : verrouille l'app au retour d'arrière-plan, masque son contenu dans le sélecteur
/// d'apps et gère la sortie rapide vers la calculatrice.
class AppLockService extends GetxService with WidgetsBindingObserver {
  AppLockService({
    required this._getPrivacySettings,
    required this._getPinLength,
    required this._verifyLockPin,
    required this._unlockWithBiometrics,
  });

  final GetPrivacySettings _getPrivacySettings;
  final GetPinLength _getPinLength;
  final VerifyLockPin _verifyLockPin;
  final UnlockWithBiometrics _unlockWithBiometrics;

  final settings = const PrivacySettings().obs;
  final isLocked = false.obs;
  final pinLength = RxnInt();

  /// Voile posé quand l'app passe en arrière-plan, pour que l'aperçu des apps récentes reste neutre.
  final isObscured = false.obs;

  /// Calculatrice affichée par la sortie rapide.
  final isDecoyActive = false.obs;

  DateTime? _backgroundedAt;
  bool _authenticating = false;

  @override
  void onInit() {
    super.onInit();
    WidgetsBinding.instance.addObserver(this);
    // Déconnexion : les réglages du mode discret sont effacés avec le reste.
    ever(Get.find<SessionService>().user, (_) => reload());
    reload(lockNow: true);
  }

  Future<void> reload({bool lockNow = false}) async {
    settings.value = await _getPrivacySettings();
    pinLength.value = await _getPinLength();
    if (!settings.value.lockEnabled) {
      isLocked.value = false;
      isDecoyActive.value = false;
    } else if (lockNow) {
      _lock();
    }
  }

  Future<void> apply(PrivacySettings updated) async {
    settings.value = updated;
    pinLength.value = await _getPinLength();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (_authenticating) {
      return;
    }
    final current = settings.value;
    switch (state) {
      case AppLifecycleState.inactive:
      case AppLifecycleState.hidden:
        isObscured.value = current.hideInRecents || current.lockEnabled;
      case AppLifecycleState.paused:
        _backgroundedAt ??= DateTime.now();
      case AppLifecycleState.resumed:
        final away = _backgroundedAt == null ? Duration.zero : DateTime.now().difference(_backgroundedAt!);
        _backgroundedAt = null;
        if (current.lockEnabled && !isDecoyActive.value && _backgroundedSince(away, current.lockDelay)) {
          _lock();
        }
        isObscured.value = false;
      case AppLifecycleState.detached:
        break;
    }
  }

  bool _backgroundedSince(Duration away, LockDelay delay) => away > Duration.zero && away >= delay.duration;

  void _lock() {
    isLocked.value = true;
    if (settings.value.biometricEnabled) {
      unlockWithBiometrics();
    }
  }

  Future<PinCheck> unlockWithPin(String pin) async {
    final check = await _verifyLockPin(pin);
    if (check is PinAccepted) {
      _unlock();
    }
    return check;
  }

  Future<void> unlockWithBiometrics() async {
    if (_authenticating) {
      return;
    }
    _authenticating = true;
    try {
      if (await _unlockWithBiometrics()) {
        _unlock();
      }
    } finally {
      _authenticating = false;
    }
  }

  /// Bascule instantanément sur la calculatrice. Le code, suivi de « = », ramène dans l'app.
  void quickExit() {
    if (settings.value.quickExitEnabled) {
      isDecoyActive.value = true;
    }
  }

  void _unlock() {
    isLocked.value = false;
    isDecoyActive.value = false;
  }

  @override
  void onClose() {
    WidgetsBinding.instance.removeObserver(this);
    super.onClose();
  }
}
