import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';
import '../../domain/entities/learning_badge.dart';

/// Médaille d'un jalon : dégradé de la marque si obtenu, grisée avec un cadenas sinon.
class BadgeMedallion extends StatelessWidget {
  const BadgeMedallion({super.key, required this.badge, this.size = 56});

  final LearningBadge badge;
  final double size;

  static IconData iconFor(String code) => switch (code) {
    'first_lesson' => Icons.directions_walk_rounded,
    'streak_3' => Icons.wb_twilight_rounded,
    'streak_7' => Icons.wb_sunny_rounded,
    'lessons_10' => Icons.auto_stories_rounded,
    'first_course' => Icons.workspace_premium_rounded,
    'three_courses' => Icons.military_tech_rounded,
    _ => Icons.star_rounded,
  };

  @override
  Widget build(BuildContext context) {
    return Semantics(
      label: '${badge.label}, ${badge.earned ? 'obtenu' : 'pas encore obtenu'}',
      child: SizedBox.square(
        dimension: size,
        child: Stack(
          clipBehavior: Clip.none,
          children: [
            Container(
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                gradient: badge.earned ? AppColors.brandGradient : null,
                color: badge.earned ? null : AppColors.surfaceMuted,
                border: Border.all(color: badge.earned ? AppColors.violet200 : AppColors.border, width: 2),
                boxShadow: badge.earned
                    ? [BoxShadow(color: AppColors.violet700.withValues(alpha: 0.25), blurRadius: 12, offset: const Offset(0, 4))]
                    : null,
              ),
              alignment: Alignment.center,
              child: Icon(iconFor(badge.code), size: size * 0.46, color: badge.earned ? AppColors.white : AppColors.textMuted),
            ),
            if (!badge.earned)
              Positioned(
                right: -2,
                bottom: -2,
                child: Container(
                  padding: const EdgeInsets.all(3),
                  decoration: const BoxDecoration(color: AppColors.white, shape: BoxShape.circle),
                  child: Icon(Icons.lock_rounded, size: size * 0.22, color: AppColors.textMuted),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
