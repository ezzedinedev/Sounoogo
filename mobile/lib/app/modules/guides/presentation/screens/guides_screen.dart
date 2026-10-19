import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/accent_title.dart';
import '../../../../core/widgets/fade_slide_in.dart';
import '../../../../core/widgets/scale_on_press.dart';
import '../../../../core/widgets/state_views.dart';
import '../controllers/guides_controller.dart';

class GuidesScreen extends GetView<GuidesController> {
  const GuidesScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Aider un proche')),
      body: Obx(() {
        if (controller.guides.isEmpty) {
          if (controller.errorMessage.value != null) {
            return ErrorState(message: controller.errorMessage.value!, onRetry: controller.load);
          }
          return controller.isLoading.value
              ? const SkeletonList(itemHeight: 110)
              : const EmptyState(icon: Icons.volunteer_activism_outlined, title: 'Bientôt disponible');
        }
        return ListView(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.xxl),
          children: [
            const AccentTitle('Quand quelqu\'un', accent: 'que tu aimes va mal', large: true),
            const SizedBox(height: AppSpacing.md),
            const Text(
              'Des repères simples pour écouter, repérer les signes d\'alerte et prendre soin de toi aussi.',
              style: AppTextStyles.bodySecondary,
            ),
            const SizedBox(height: AppSpacing.xl),
            for (final (index, guide) in controller.guides.indexed)
              FadeSlideIn(
                index: index,
                child: Padding(
                  padding: const EdgeInsets.only(bottom: AppSpacing.md),
                  child: ScaleOnPress(
                    child: Card(
                      clipBehavior: Clip.antiAlias,
                      child: InkWell(
                        onTap: () => Get.toNamed(AppRoutes.guide(guide.slug)),
                        child: Padding(
                          padding: const EdgeInsets.all(AppSpacing.lg),
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                '${index + 1}'.padLeft(2, '0'),
                                style: AppTextStyles.figure.copyWith(fontSize: 30, color: AppColors.violet300),
                              ),
                              const SizedBox(width: AppSpacing.lg),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(guide.title, style: AppTextStyles.subtitle),
                                    const SizedBox(height: AppSpacing.xs),
                                    Text(guide.summary, style: AppTextStyles.caption),
                                    const SizedBox(height: AppSpacing.sm),
                                    Text(
                                      '${guide.readingMinutes} min de lecture',
                                      style: AppTextStyles.caption.copyWith(color: AppColors.violet700),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
          ],
        );
      }),
    );
  }
}
