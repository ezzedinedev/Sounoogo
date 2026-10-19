import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../services/app_lock_service.dart';

/// Sortie rapide : visible seulement si elle est activée dans le mode discret.
class QuickExitButton extends StatelessWidget {
  const QuickExitButton({super.key, this.color = AppColors.textSecondary});

  final Color color;

  @override
  Widget build(BuildContext context) {
    final lock = Get.find<AppLockService>();
    return Obx(
      () => lock.settings.value.quickExitEnabled
          ? IconButton(
              tooltip: 'Sortie rapide',
              onPressed: lock.quickExit,
              icon: Icon(Icons.visibility_off_outlined, color: color),
            )
          : const SizedBox.shrink(),
    );
  }
}
