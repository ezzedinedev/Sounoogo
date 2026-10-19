import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../routes/app_routes.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_spacing.dart';
import '../../../core/theme/app_text_styles.dart';
import '../../../core/widgets/brand_mark.dart';
import '../../../core/widgets/emergency_sheet.dart';
import '../controllers/auth_controller.dart';

/// Premier écran : parler tout de suite, sans aucune donnée personnelle.
class WelcomeScreen extends GetView<AuthController> {
  const WelcomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.violet950,
      body: DecoratedBox(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [AppColors.violet800, AppColors.violet950],
          ),
        ),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.all(AppSpacing.page),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const BrandMark(size: 36, inverse: true),
                    const SizedBox(width: AppSpacing.sm),
                    Text('SUNOOGO', style: AppTextStyles.label.copyWith(color: AppColors.white, letterSpacing: 1.5)),
                    const Spacer(),
                    TextButton.icon(
                      onPressed: () => EmergencySheet.show(alertSupervisor: false),
                      icon: const Icon(Icons.call_rounded, size: 18),
                      label: const Text('Urgence'),
                      style: TextButton.styleFrom(foregroundColor: AppColors.lilac200),
                    ),
                  ],
                ),
                const Spacer(),
                Text('Parle librement.', style: AppTextStyles.display.copyWith(color: AppColors.white, fontSize: 40)),
                Text("quelqu'un t'écoute.", style: AppTextStyles.displayAccent.copyWith(color: AppColors.lilac200, fontSize: 44)),
                const SizedBox(height: AppSpacing.lg),
                Text(
                  'Des écoutants formés te répondent, sans jugement. Pas de nom, pas de numéro : tu restes anonyme.',
                  style: AppTextStyles.body.copyWith(color: AppColors.white.withValues(alpha: 0.8), fontSize: 16),
                ),
                const SizedBox(height: AppSpacing.xl),
                Wrap(
                  spacing: AppSpacing.sm,
                  runSpacing: AppSpacing.sm,
                  children: const [
                    _Promise(icon: Icons.visibility_off_outlined, label: 'Anonyme'),
                    _Promise(icon: Icons.favorite_border_rounded, label: 'Gratuit'),
                    _Promise(icon: Icons.lock_outline_rounded, label: 'Chiffré'),
                  ],
                ),
                const SizedBox(height: AppSpacing.xxl),
                Obx(
                  () => Column(
                    children: [
                      if (controller.errorMessage.value != null) ...[
                        Text(controller.errorMessage.value!, style: AppTextStyles.caption.copyWith(color: AppColors.lilac200)),
                        const SizedBox(height: AppSpacing.md),
                      ],
                      FilledButton(
                        onPressed: controller.isLoading.value ? null : controller.startAnonymous,
                        style: FilledButton.styleFrom(backgroundColor: AppColors.white, foregroundColor: AppColors.violet800),
                        child: controller.isLoading.value
                            ? const SizedBox.square(dimension: 22, child: CircularProgressIndicator(strokeWidth: 2.4))
                            : const Text('Commencer à parler'),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: AppSpacing.md),
                OutlinedButton(
                  onPressed: () => Get.toNamed(AppRoutes.recover),
                  style: OutlinedButton.styleFrom(foregroundColor: AppColors.white, side: BorderSide(color: AppColors.white.withValues(alpha: 0.3))),
                  child: const Text("J'ai déjà un code de récupération"),
                ),
                const SizedBox(height: AppSpacing.sm),
                Center(
                  child: TextButton(
                    onPressed: () => Get.toNamed(AppRoutes.login),
                    style: TextButton.styleFrom(foregroundColor: AppColors.lilac200),
                    child: const Text('Se connecter avec un e-mail ou un téléphone'),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _Promise extends StatelessWidget {
  const _Promise({required this.icon, required this.label});

  final IconData icon;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: AppSpacing.sm),
      decoration: BoxDecoration(
        color: AppColors.white.withValues(alpha: 0.08),
        borderRadius: BorderRadius.circular(AppSpacing.radiusPill),
        border: Border.all(color: AppColors.white.withValues(alpha: 0.15)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 16, color: AppColors.lilac200),
          const SizedBox(width: AppSpacing.xs + 2),
          Text(label, style: AppTextStyles.caption.copyWith(color: AppColors.white, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}
