import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../../../../core/widgets/accent_title.dart';
import '../../../../core/widgets/state_views.dart';
import '../controllers/courses_controller.dart';
import '../../domain/entities/course.dart';
import '../../domain/entities/enrollment.dart';
import '../widgets/course_cover.dart';
import '../widgets/learning_progress_card.dart';
import '../../../../core/widgets/fade_slide_in.dart';
import '../../../../core/widgets/scale_on_press.dart';

class CoursesTab extends GetView<CoursesController> {
  const CoursesTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Formations'),
        actions: [
          TextButton(onPressed: () => Get.toNamed(AppRoutes.subscription), child: const Text('Abonnement')),
          const SizedBox(width: AppSpacing.sm),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: controller.load,
        child: Obx(() {
          if (controller.isLoading.value && controller.items.isEmpty) {
            return const SkeletonList(count: 3, itemHeight: 240);
          }
          if (controller.errorMessage.value != null) {
            return ErrorState(message: controller.errorMessage.value!, onRetry: controller.load);
          }
          return ListView(
            padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.xxl),
            children: [
              const AccentTitle('Des parcours pour', accent: 'reprendre confiance', large: true),
              const SizedBox(height: AppSpacing.md),
              const Text(
                'Quelques minutes par jour, des exercices simples. La première leçon est toujours offerte.',
                style: AppTextStyles.bodySecondary,
              ),
              if (controller.progress.value case final progress?) ...[
                const SizedBox(height: AppSpacing.xl),
                FadeSlideIn(child: LearningProgressCard(progress: progress)),
              ],
              const SizedBox(height: AppSpacing.xl),
              if (controller.items.isEmpty)
                const EmptyState(
                  icon: Icons.auto_stories_outlined,
                  title: 'Bientôt disponible',
                  message: 'Les premières formations arrivent.',
                )
              else
                for (final (index, course) in controller.items.indexed) ...[
                  FadeSlideIn(
                    index: index,
                    child: _CourseCard(course: course, enrollment: controller.enrollments[course.slug]),
                  ),
                  const SizedBox(height: AppSpacing.lg),
                ],
            ],
          );
        }),
      ),
    );
  }
}

class _CourseCard extends StatelessWidget {
  const _CourseCard({required this.course, this.enrollment});

  final Course course;
  final Enrollment? enrollment;

  @override
  Widget build(BuildContext context) {
    return ScaleOnPress(
      child: Card(
        clipBehavior: Clip.antiAlias,
        child: InkWell(
          onTap: () => Get.toNamed(AppRoutes.course(course.slug)),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              CourseCover(url: course.coverUrl, heroTag: 'course-${course.slug}'),
              Padding(
                padding: const EdgeInsets.all(AppSpacing.lg),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      '${course.levelLabel.toUpperCase()} · ${course.lessonsCount ?? 0} LEÇONS · ${course.durationMinutes ?? 0} MIN',
                      style: AppTextStyles.caption.copyWith(color: AppColors.violet700, fontWeight: FontWeight.w600, letterSpacing: 0.6),
                    ),
                    const SizedBox(height: AppSpacing.sm),
                    Text(course.title, style: AppTextStyles.subtitle),
                    const SizedBox(height: AppSpacing.xs),
                    Text(course.summary, style: AppTextStyles.bodySecondary, maxLines: 2, overflow: TextOverflow.ellipsis),
                    const SizedBox(height: AppSpacing.md),
                    if (enrollment != null) ...[
                      ClipRRect(
                        borderRadius: BorderRadius.circular(AppSpacing.radiusPill),
                        // La progression se remplit à l'affichage.
                        child: TweenAnimationBuilder<double>(
                          tween: Tween(begin: 0, end: enrollment!.progress / 100),
                          duration: const Duration(milliseconds: 900),
                          curve: Curves.easeOutCubic,
                          builder: (_, value, _) => LinearProgressIndicator(
                            value: value,
                            minHeight: 6,
                            backgroundColor: AppColors.violet100,
                            color: AppColors.violet700,
                          ),
                        ),
                      ),
                      const SizedBox(height: AppSpacing.xs),
                      Text('${enrollment!.progress} % terminé', style: AppTextStyles.caption),
                    ] else
                      Text(
                        course.isFree ? 'Offerte' : '${Formatters.money(course.price)} ou incluse dans l\'abonnement',
                        style: AppTextStyles.label.copyWith(color: course.isFree ? AppColors.violet700 : AppColors.textPrimary),
                      ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
