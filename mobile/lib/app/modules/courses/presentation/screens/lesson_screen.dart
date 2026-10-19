import 'package:flutter/material.dart';
import 'package:flutter_markdown_plus/flutter_markdown_plus.dart';
import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/state_views.dart';
import '../controllers/lesson_controller.dart';

class LessonScreen extends GetView<LessonController> {
  const LessonScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(),
      body: Obx(() {
        final lesson = controller.lesson.value;
        if (lesson == null) {
          return controller.errorMessage.value != null
              ? ErrorState(message: controller.errorMessage.value!, onRetry: controller.load)
              : const SkeletonList(count: 4, itemHeight: 60);
        }
        return ListView(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, 0, AppSpacing.page, 120),
          children: [
            Text(
              'Leçon ${lesson.position} · ${lesson.durationMinutes} min',
              style: AppTextStyles.caption.copyWith(color: AppColors.violet700),
            ),
            const SizedBox(height: AppSpacing.sm),
            Text(lesson.title, style: AppTextStyles.title),
            if (lesson.videoUrl != null) ...[
              const SizedBox(height: AppSpacing.lg),
              OutlinedButton.icon(
                onPressed: () => launchUrl(Uri.parse(lesson.videoUrl!)),
                icon: const Icon(Icons.play_circle_outline_rounded),
                label: const Text('Regarder la vidéo'),
              ),
            ],
            const SizedBox(height: AppSpacing.lg),
            MarkdownBody(
              data: lesson.content ?? '',
              styleSheet: MarkdownStyleSheet(
                p: AppTextStyles.body.copyWith(fontSize: 16, height: 1.65),
                h2: AppTextStyles.subtitle,
                strong: AppTextStyles.body.copyWith(fontWeight: FontWeight.w700),
                blockquoteDecoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusSm)),
              ),
            ),
          ],
        );
      }),
      bottomNavigationBar: SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.md),
          child: Obx(
            () => FilledButton(
              onPressed: controller.isCompleting.value || controller.lesson.value == null ? null : controller.complete,
              child: const Text('J\'ai terminé cette leçon'),
            ),
          ),
        ),
      ),
    );
  }
}
