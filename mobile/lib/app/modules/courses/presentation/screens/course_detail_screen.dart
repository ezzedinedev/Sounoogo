import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../../../../core/widgets/state_views.dart';
import '../controllers/course_detail_controller.dart';
import '../widgets/course_cover.dart';
import '../../../../core/widgets/fade_slide_in.dart';

class CourseDetailScreen extends GetView<CourseDetailController> {
  const CourseDetailScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Obx(() {
        final course = controller.course.value;
        if (course == null) {
          return SafeArea(
            child: controller.errorMessage.value != null
                ? ErrorState(message: controller.errorMessage.value!, onRetry: controller.load)
                : const SkeletonList(count: 3),
          );
        }

        final enrollment = controller.enrollment.value;
        final completed = enrollment?.completedLessonIds ?? const <int>[];

        return CustomScrollView(
          slivers: [
            SliverAppBar(
              expandedHeight: 240,
              pinned: true,
              backgroundColor: AppColors.violet800,
              foregroundColor: AppColors.white,
              flexibleSpace: FlexibleSpaceBar(
                background: CourseCover(url: course.coverUrl, height: 240, heroTag: 'course-${course.slug}'),
              ),
            ),
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.xl, AppSpacing.page, 120),
              sliver: SliverList.list(
                children: [
                  Text(
                    course.levelLabel.toUpperCase(),
                    style: AppTextStyles.caption.copyWith(color: AppColors.violet700, fontWeight: FontWeight.w600, letterSpacing: 0.6),
                  ),
                  const SizedBox(height: AppSpacing.sm),
                  Text(course.title, style: AppTextStyles.title),
                  const SizedBox(height: AppSpacing.md),
                  Text(course.description ?? course.summary, style: AppTextStyles.bodySecondary),
                  if (enrollment != null) ...[
                    const SizedBox(height: AppSpacing.lg),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(AppSpacing.radiusPill),
                      child: TweenAnimationBuilder<double>(
                        tween: Tween(begin: 0, end: enrollment.progress / 100),
                        duration: const Duration(milliseconds: 900),
                        curve: Curves.easeOutCubic,
                        builder: (_, value, _) => LinearProgressIndicator(
                          value: value,
                          minHeight: 8,
                          backgroundColor: AppColors.violet100,
                          color: AppColors.violet700,
                        ),
                      ),
                    ),
                    const SizedBox(height: AppSpacing.xs),
                    Text('${enrollment.progress} % terminé', style: AppTextStyles.caption),
                  ],
                  const SizedBox(height: AppSpacing.xl),
                  const Text('Leçons', style: AppTextStyles.subtitle),
                  const SizedBox(height: AppSpacing.sm),
                  for (final (index, lesson) in course.lessons.indexed)
                    FadeSlideIn(
                      index: index + 2,
                      child: ListTile(
                        contentPadding: EdgeInsets.zero,
                        onTap: () => controller.openLesson(lesson),
                        leading: CircleAvatar(
                          backgroundColor: completed.contains(lesson.id) ? AppColors.violet700 : AppColors.violet50,
                          child: completed.contains(lesson.id)
                              ? const Icon(Icons.check_rounded, color: AppColors.white, size: 20)
                              : Text('${lesson.position}', style: AppTextStyles.label.copyWith(color: AppColors.violet800)),
                        ),
                        title: Text(lesson.title, style: AppTextStyles.label),
                        subtitle: Text(
                          '${lesson.durationMinutes} min${lesson.isPreview ? ' · aperçu gratuit' : ''}',
                          style: AppTextStyles.caption,
                        ),
                        trailing: Icon(
                          enrollment != null || lesson.isPreview ? Icons.play_circle_outline_rounded : Icons.lock_outline_rounded,
                          color: AppColors.violet700,
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ],
        );
      }),
      bottomNavigationBar: Obx(() {
        final course = controller.course.value;
        if (course == null || controller.enrollment.value != null) {
          return const SizedBox.shrink();
        }
        return SafeArea(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.md),
            child: FilledButton(
              onPressed: controller.isEnrolling.value ? null : controller.enroll,
              child: Text(course.isFree ? 'Commencer le parcours' : 'Commencer · ${Formatters.money(course.price)}'),
            ),
          ),
        );
      }),
    );
  }
}
