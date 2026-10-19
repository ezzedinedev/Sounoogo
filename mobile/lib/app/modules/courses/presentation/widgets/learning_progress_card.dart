import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../../../../core/widgets/app_sheet.dart';
import '../../../../core/widgets/scale_on_press.dart';
import '../../domain/entities/learning_badge.dart';
import '../../domain/entities/learning_progress.dart';
import 'badge_medallion.dart';

/// Série de jours et jalons, en tête de l'onglet Formations.
class LearningProgressCard extends StatelessWidget {
  const LearningProgressCard({super.key, required this.progress});

  final LearningProgress progress;

  @override
  Widget build(BuildContext context) {
    final streak = progress.currentStreak;

    return Container(
      padding: const EdgeInsets.all(AppSpacing.lg),
      decoration: BoxDecoration(
        color: AppColors.surface,
        border: Border.all(color: AppColors.border),
        borderRadius: BorderRadius.circular(AppSpacing.radiusLg),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              _StreakSun(active: progress.activeToday, lit: streak > 0),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    TweenAnimationBuilder<double>(
                      tween: Tween(begin: 0, end: streak.toDouble()),
                      duration: Duration(milliseconds: MediaQuery.disableAnimationsOf(context) ? 0 : 700),
                      curve: Curves.easeOutCubic,
                      builder: (_, value, _) => Text(
                        streak == 0 ? 'Prêt·e à commencer ?' : '${value.round()} jour${streak > 1 ? 's' : ''} d\'affilée',
                        style: AppTextStyles.subtitle,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(_streakHint(), style: AppTextStyles.caption),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.lg),
          Row(
            children: [
              Text('Jalons', style: AppTextStyles.label),
              const SizedBox(width: AppSpacing.sm),
              Text('${progress.earnedCount}/${progress.badges.length}', style: AppTextStyles.caption),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          SizedBox(
            height: 60,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              clipBehavior: Clip.none,
              itemCount: progress.badges.length,
              separatorBuilder: (_, _) => const SizedBox(width: AppSpacing.md),
              itemBuilder: (_, index) {
                final badge = progress.badges[index];
                return ScaleOnPress(
                  child: GestureDetector(
                    onTap: () => AppSheet.show<void>(_BadgeSheet(badge: badge)),
                    child: BadgeMedallion(badge: badge),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  String _streakHint() {
    if (progress.activeToday) return 'Leçon du jour faite. À demain !';
    if (progress.currentStreak > 0) return 'Une leçon aujourd\'hui prolonge ta série.';
    if (progress.bestStreak > 0) return 'Ta meilleure série : ${progress.bestStreak} jours. Une leçon suffit pour repartir.';
    return 'Une leçon par jour, à ton rythme.';
  }
}

/// Soleil levant (le symbole de la marque) : il « brille » quand une leçon a été faite aujourd'hui.
class _StreakSun extends StatelessWidget {
  const _StreakSun({required this.active, required this.lit});

  final bool active;
  final bool lit;

  @override
  Widget build(BuildContext context) {
    return AnimatedContainer(
      duration: const Duration(milliseconds: 400),
      width: 48,
      height: 48,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        gradient: active ? AppColors.brandGradient : null,
        color: active ? null : (lit ? AppColors.violet100 : AppColors.surfaceMuted),
      ),
      child: Icon(Icons.wb_sunny_rounded, color: active ? AppColors.white : (lit ? AppColors.violet700 : AppColors.textMuted)),
    );
  }
}

class _BadgeSheet extends StatelessWidget {
  const _BadgeSheet({required this.badge});

  final LearningBadge badge;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.md, AppSpacing.page, AppSpacing.xxl),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          BadgeMedallion(badge: badge, size: 88),
          const SizedBox(height: AppSpacing.lg),
          Text(badge.label, style: AppTextStyles.subtitle, textAlign: TextAlign.center),
          const SizedBox(height: AppSpacing.sm),
          Text(badge.description, style: AppTextStyles.bodySecondary, textAlign: TextAlign.center),
          const SizedBox(height: AppSpacing.md),
          Text(
            badge.earned && badge.awardedAt != null ? 'Obtenu le ${Formatters.day(badge.awardedAt!)}' : 'Pas encore obtenu',
            style: AppTextStyles.caption,
          ),
        ],
      ),
    );
  }
}
