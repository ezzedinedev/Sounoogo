import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../controllers/emergency_controller.dart';

/// Panneau d'urgence : prévient un superviseur et affiche les numéros locaux.
/// Toujours accessible, même hors ligne (numéros par défaut).
abstract final class EmergencySheet {
  static Future<void> show({bool alertSupervisor = true}) async {
    final controller = Get.find<EmergencyController>();
    final alerted = alertSupervisor && controller.canAlertSupervisor;
    if (alerted) {
      controller.alertSupervisor();
    }

    await Get.bottomSheet<void>(
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
                    const Expanded(child: Text("Tu n'es pas seul·e", style: AppTextStyles.title)),
                  ],
                ),
                const SizedBox(height: AppSpacing.md),
                Text(
                  alerted
                      ? 'Un superviseur SUNOOGO vient d\'être prévenu. Si tu es en danger immédiat, appelle maintenant :'
                      : 'Si tu es en danger immédiat, appelle maintenant :',
                  style: AppTextStyles.bodySecondary,
                ),
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
              ],
            ),
          ),
        ),
      ),
      isScrollControlled: true,
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
        child: const Text('SOS'),
      ),
    );
  }
}
