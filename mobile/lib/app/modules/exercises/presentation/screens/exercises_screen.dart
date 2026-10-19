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
import '../../domain/entities/exercise.dart';
import '../controllers/exercises_controller.dart';

/// Boîte à outils : exercices courts pour s'apaiser, disponibles même sans connexion.
class ExercisesScreen extends GetView<ExercisesController> {
  const ExercisesScreen({super.key});

  static const _icons = {
    'breathing': Icons.air_rounded,
    'grounding': Icons.spa_outlined,
    'relaxation': Icons.self_improvement_rounded,
    'sleep': Icons.bedtime_outlined,
  };

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Exercices')),
      body: Obx(() {
        final library = controller.library.value;
        if (library == null) {
          return controller.errorMessage.value != null
              ? ErrorState(message: controller.errorMessage.value!, onRetry: controller.load)
              : const SkeletonList(itemHeight: 96);
        }
        final exercises = controller.visible;

        return RefreshIndicator(
          onRefresh: controller.load,
          child: ListView(
            padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.xxl),
            children: [
              const AccentTitle('Quelques minutes pour', accent: 'souffler', large: true),
              const SizedBox(height: AppSpacing.md),
              const Text('Des exercices simples, utilisables partout, même sans connexion.', style: AppTextStyles.bodySecondary),
              if (library.fromCache) ...[const SizedBox(height: AppSpacing.md), const _OfflineBanner()],
              const SizedBox(height: AppSpacing.lg),
              SizedBox(
                height: 40,
                child: ListView(
                  scrollDirection: Axis.horizontal,
                  children: [
                    _CategoryChip(
                      label: 'Tous',
                      selected: controller.category.value == null,
                      onTap: () => controller.category.value = null,
                    ),
                    for (final entry in controller.categories.entries)
                      _CategoryChip(
                        label: entry.value,
                        selected: controller.category.value == entry.key,
                        onTap: () => controller.category.value = entry.key,
                      ),
                  ],
                ),
              ),
              const SizedBox(height: AppSpacing.lg),
              if (exercises.isEmpty)
                const EmptyState(icon: Icons.spa_outlined, title: 'Bientôt disponible', message: 'Les premiers exercices arrivent.')
              else
                for (final (index, exercise) in exercises.indexed)
                  FadeSlideIn(
                    key: ValueKey('${controller.category.value}-${exercise.slug}'),
                    index: index,
                    child: _ExerciseCard(exercise: exercise, icon: _icons[exercise.category] ?? Icons.spa_outlined),
                  ),
            ],
          ),
        );
      }),
    );
  }
}

class _OfflineBanner extends StatelessWidget {
  const _OfflineBanner();

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(color: AppColors.warningSoft, borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
      child: Row(
        children: [
          const Icon(Icons.cloud_off_rounded, color: AppColors.warning, size: 20),
          const SizedBox(width: AppSpacing.sm),
          Expanded(
            child: Text(
              'Hors ligne : version enregistrée sur ton téléphone.',
              style: AppTextStyles.caption.copyWith(color: AppColors.warning),
            ),
          ),
        ],
      ),
    );
  }
}

class _CategoryChip extends StatelessWidget {
  const _CategoryChip({required this.label, required this.selected, required this.onTap});

  final String label;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(right: AppSpacing.sm),
      child: ChoiceChip(
        label: Text(label),
        selected: selected,
        showCheckmark: false,
        labelStyle: AppTextStyles.label.copyWith(color: selected ? AppColors.white : AppColors.textPrimary),
        onSelected: (_) => onTap(),
      ),
    );
  }
}

class _ExerciseCard extends StatelessWidget {
  const _ExerciseCard({required this.exercise, required this.icon});

  final Exercise exercise;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: AppSpacing.md),
      child: ScaleOnPress(
        child: Card(
          clipBehavior: Clip.antiAlias,
          child: InkWell(
            onTap: () => Get.toNamed(AppRoutes.exercise(exercise.slug)),
            child: Padding(
              padding: const EdgeInsets.all(AppSpacing.lg),
              child: Row(
                children: [
                  Hero(
                    tag: 'exercise-${exercise.slug}',
                    child: Container(
                      width: 52,
                      height: 52,
                      decoration: BoxDecoration(
                        gradient: AppColors.brandGradient,
                        borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
                      ),
                      child: Icon(icon, color: AppColors.white),
                    ),
                  ),
                  const SizedBox(width: AppSpacing.lg),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          '${exercise.categoryLabel.toUpperCase()} · ${exercise.durationMinutes} MIN',
                          style: AppTextStyles.caption.copyWith(
                            color: AppColors.violet700,
                            fontWeight: FontWeight.w600,
                            letterSpacing: 0.6,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(exercise.title, style: AppTextStyles.subtitle),
                        const SizedBox(height: 2),
                        Text(exercise.summary, style: AppTextStyles.caption, maxLines: 2, overflow: TextOverflow.ellipsis),
                      ],
                    ),
                  ),
                  if (exercise.audioUrl != null) ...[
                    const SizedBox(width: AppSpacing.sm),
                    const Icon(Icons.headphones_rounded, color: AppColors.violet500, size: 20),
                  ],
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
