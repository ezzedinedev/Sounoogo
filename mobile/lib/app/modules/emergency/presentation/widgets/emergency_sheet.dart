import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/l10n/app_locales.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/app_sheet.dart';
import '../controllers/emergency_controller.dart';

/// Panneau d'urgence : prévient un superviseur et affiche les numéros locaux.
/// Toujours accessible, même hors ligne (numéros par défaut).
abstract final class EmergencySheet {
  /// Ferme le panneau puis ouvre l'écran demandé (plan de sécurité, respiration).
  static void _leaveTo(String route) {
    Get.back<void>();
    Get.toNamed(route);
  }

  static Future<void> show({bool alertSupervisor = true}) async {
    final controller = Get.find<EmergencyController>();
    final alerted = alertSupervisor && controller.canAlertSupervisor;
    if (alerted) {
      controller.alertSupervisor();
    }

    final l10n = Get.context!.l10n;
    await AppSheet.show<void>(
      SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, 0, AppSpacing.page, AppSpacing.xl),
          child: Obx(
            () => Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(AppSpacing.sm),
                      decoration: BoxDecoration(color: AppColors.sosSoft, borderRadius: BorderRadius.circular(AppSpacing.radiusSm)),
                      child: const Icon(Icons.health_and_safety_rounded, color: AppColors.sos),
                    ),
                    const SizedBox(width: AppSpacing.md),
                    Expanded(child: Text(l10n.emergencyTitle, style: AppTextStyles.title)),
                  ],
                ),
                const SizedBox(height: AppSpacing.md),
                Text(alerted ? l10n.emergencySupervisorAlerted : l10n.emergencyCallNow, style: AppTextStyles.bodySecondary),
                const SizedBox(height: AppSpacing.lg),
                for (final contact in controller.contacts) ...[
                  Material(
                    color: AppColors.sosSoft,
                    borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
                    child: InkWell(
                      borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
                      onTap: () => launchUrl(Uri(scheme: 'tel', path: contact.phone)),
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg, vertical: AppSpacing.md + 2),
                        child: Row(
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(contact.label, style: AppTextStyles.label),
                                  if (contact.description != null) Text(contact.description!, style: AppTextStyles.caption),
                                ],
                              ),
                            ),
                            const Icon(Icons.call_rounded, color: AppColors.sos, size: 20),
                            const SizedBox(width: AppSpacing.sm),
                            Text(contact.phone, style: AppTextStyles.figure.copyWith(fontSize: 30, color: AppColors.sos)),
                          ],
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: AppSpacing.sm),
                ],
                if (controller.canAlertSupervisor) ...[
                  const SizedBox(height: AppSpacing.md),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () => _leaveTo(AppRoutes.safetyPlan),
                          icon: const Icon(Icons.shield_moon_outlined, size: 18),
                          label: Text(l10n.emergencyMyPlan),
                        ),
                      ),
                      const SizedBox(width: AppSpacing.sm),
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () => _leaveTo(AppRoutes.breathing),
                          icon: const Icon(Icons.air_rounded, size: 18),
                          label: Text(l10n.emergencyBreathe),
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }
}

/// Bouton SOS compact, présent dans les en-têtes de l'app.
class SosButton extends StatelessWidget {
  const SosButton({super.key});

  @override
  Widget build(BuildContext context) {
    return Semantics(
      button: true,
      label: "Urgence : afficher les numéros et prévenir un superviseur",
      child: FilledButton(
        onPressed: EmergencySheet.show,
        style: FilledButton.styleFrom(
          backgroundColor: AppColors.sos,
          minimumSize: const Size(64, 38),
          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
          textStyle: AppTextStyles.label.copyWith(letterSpacing: 0.8),
        ),
        child: Text(context.l10n.sosButton),
      ),
    );
  }
}
