import 'package:flutter/foundation.dart';
import 'package:flutter/services.dart';
import 'package:local_auth/local_auth.dart';

/// Fonctions du téléphone : biométrie et protection de l'écran (canal natif `app.sunoogo/privacy`).
class DeviceSecurityDataSource {
  DeviceSecurityDataSource({LocalAuthentication? auth}) : _auth = auth ?? LocalAuthentication();

  static const _channel = MethodChannel('app.sunoogo/privacy');

  final LocalAuthentication _auth;

  Future<bool> canUseBiometrics() async {
    // Aucune biométrie sur le web (aperçu de développement).
    if (kIsWeb) {
      return false;
    }
    try {
      return await _auth.isDeviceSupported() && (await _auth.getAvailableBiometrics()).isNotEmpty;
    } on PlatformException {
      return false;
    } on MissingPluginException {
      return false;
    }
  }

  Future<bool> authenticate(String reason) async {
    if (kIsWeb) {
      return false;
    }
    try {
      return await _auth.authenticate(localizedReason: reason, biometricOnly: true);
    } on LocalAuthException {
      return false;
    } on PlatformException {
      return false;
    } on MissingPluginException {
      return false;
    }
  }

  /// Android : FLAG_SECURE (rien dans les apps récentes, pas de capture). Sans effet ailleurs :
  /// l'app masque alors son contenu elle-même quand elle passe en arrière-plan.
  Future<void> setSecureScreen(bool enabled) async {
    // Sur le web, un canal sans gestionnaire ne répond jamais : on ne l'appelle pas.
    if (kIsWeb) {
      return;
    }
    try {
      await _channel.invokeMethod<void>('setSecureScreen', enabled);
    } on MissingPluginException {
      // Plateforme sans canal natif (iOS, web) : le voile de confidentialité suffit.
    }
  }
}
