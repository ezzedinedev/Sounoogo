import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';

import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_spacing.dart';
import '../../../core/theme/app_text_styles.dart';
import '../../../core/widgets/accent_title.dart';
import '../controllers/auth_controller.dart';

/// Montre une seule fois le pseudonyme et le code de récupération.
class RecoveryCodeScreen extends GetView<AuthController> {
  const RecoveryCodeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final result = controller.pendingResult.value;

    return Scaffold(
      appBar: AppBar(),
      body: result == null
          ? const SizedBox.shrink()
          : SafeArea(
              child: ListView(
                padding: const EdgeInsets.all(AppSpacing.page),
                children: [
                  const AccentTitle('Garde précieusement', accent: 'ton code', large: true),
                  const SizedBox(height: AppSpacing.md),
                  const Text(
                    "C'est la seule façon de retrouver tes conversations sur un autre téléphone. Nous ne le connaissons pas et ne pourrons pas te le renvoyer.",
                    style: AppTextStyles.bodySecondary,
                  ),
                  const SizedBox(height: AppSpacing.xl),
                  _Field(label: 'Ton pseudonyme', value: result.pseudonym),
                  const SizedBox(height: AppSpacing.md),
                  _Field(label: 'Ton code de récupération', value: result.recoveryCode ?? '', monospace: true),
                  const SizedBox(height: AppSpacing.xl),
                  Obx(
                    () => CheckboxListTile(
                      value: controller.codeSaved.value,
                      onChanged: (value) => controller.codeSaved.value = value ?? false,
                      contentPadding: EdgeInsets.zero,
                      controlAffinity: ListTileControlAffinity.leading,
                      title: const Text("J'ai noté mon pseudonyme et mon code", style: AppTextStyles.body),
                    ),
                  ),
                  const SizedBox(height: AppSpacing.md),
                  Obx(
                    () => FilledButton(
                      onPressed: controller.codeSaved.value ? controller.confirmRecoveryCode : null,
                      child: const Text('Continuer'),
                    ),
                  ),
                ],
              ),
            ),
    );
  }
}

class _Field extends StatelessWidget {
  const _Field({required this.label, required this.value, this.monospace = false});

  final String label;
  final String value;
  final bool monospace;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.lg),
      decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: AppTextStyles.caption),
                const SizedBox(height: AppSpacing.xs),
                SelectableText(
                  value,
                  style: AppTextStyles.title.copyWith(fontFamily: monospace ? 'monospace' : null, letterSpacing: monospace ? 1.5 : null),
                ),
              ],
            ),
          ),
          IconButton(
            tooltip: 'Copier',
            onPressed: () {
              Clipboard.setData(ClipboardData(text: value));
              Get.snackbar('Copié', label, snackPosition: SnackPosition.BOTTOM, colorText: AppColors.white, backgroundColor: AppColors.violet950);
            },
            icon: const Icon(Icons.copy_rounded, color: AppColors.violet700),
          ),
        ],
      ),
    );
  }
}
