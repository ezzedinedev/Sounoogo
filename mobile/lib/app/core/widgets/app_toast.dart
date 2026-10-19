import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../theme/app_colors.dart';
import '../theme/app_spacing.dart';

/// Message bref en bas d'écran, aux couleurs de l'app.
abstract final class AppToast {
  static void show(String title, String message) => Get.snackbar(
    title,
    message,
    snackPosition: SnackPosition.BOTTOM,
    backgroundColor: AppColors.violet950,
    colorText: AppColors.white,
    borderRadius: AppSpacing.radiusMd,
    margin: const EdgeInsets.fromLTRB(AppSpacing.lg, 0, AppSpacing.lg, AppSpacing.xl),
    duration: const Duration(seconds: 3),
  );
}
