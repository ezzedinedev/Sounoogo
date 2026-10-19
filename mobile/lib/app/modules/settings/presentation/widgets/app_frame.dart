import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/widgets/brand_mark.dart';
import '../services/app_lock_service.dart';
import '../services/display_service.dart';
import 'decoy_calculator.dart';
import 'lock_screen.dart';

/// Enveloppe toute l'app (via `GetMaterialApp.builder`) : taille du texte choisie,
/// voile de confidentialité, écran de verrouillage et calculatrice de sortie rapide.
class AppFrame extends StatelessWidget {
  const AppFrame({super.key, required this.child});

  final Widget child;

  /// Au-delà, les mises en page ne tiennent plus ; le réglage du téléphone reste respecté jusque-là.
  static const _maxTextScale = 2.0;

  @override
  Widget build(BuildContext context) {
    final lock = Get.find<AppLockService>();
    final display = Get.find<DisplayService>();
    final media = MediaQuery.of(context);

    return Obx(() {
      final scale = (media.textScaler.scale(1) * display.textSize.value.factor).clamp(1.0, _maxTextScale);
      return MediaQuery(
        data: media.copyWith(textScaler: TextScaler.linear(scale)),
        child: Stack(
          children: [
            child,
            if (lock.isDecoyActive.value)
              const Positioned.fill(child: DecoyCalculator())
            else if (lock.isLocked.value)
              const Positioned.fill(child: LockScreen()),
            if (lock.isObscured.value) const Positioned.fill(child: _PrivacyCover()),
          ],
        ),
      );
    });
  }
}

class _PrivacyCover extends StatelessWidget {
  const _PrivacyCover();

  @override
  Widget build(BuildContext context) {
    return const ColoredBox(
      color: AppColors.violet950,
      child: Center(child: BrandMark(size: 64, inverse: true)),
    );
  }
}
