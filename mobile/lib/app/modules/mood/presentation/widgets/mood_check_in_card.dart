import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/pressable.dart';
import '../../domain/entities/mood_level.dart';
import '../../domain/entities/mood_summary.dart';
import '../controllers/mood_controller.dart';
import 'mood_check_in_sheet.dart';
import 'mood_face.dart';
import 'mood_suggestion_card.dart';

/// Check-in d'humeur de l'accueil, en un geste.
class MoodCheckInCard extends GetView<MoodController> {
  const MoodCheckInCard({super.key, required this.onTalk});

  /// Ouvre l'onglet d'écoute.
  final VoidCallback onTalk;

  @override
  Widget build(BuildContext context) {
    return Obx(() {
      final journal = controller.journal.value;
      final today = journal?.today;
      final summary = journal?.summary ?? MoodSummary.empty;

      return Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Card(
            child: Padding(
              padding: const EdgeInsets.all(AppSpacing.lg),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          today == null ? 'Comment te sens-tu ?' : 'Aujourd\'hui : ${today.level.label.toLowerCase()}',
                          style: AppTextStyles.subtitle,
                        ),
                      ),
                      Pressable(
                        onTap: () => Get.toNamed(AppRoutes.mood),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            if (summary.streak > 1) ...[
                              const Icon(Icons.local_fire_department_rounded, size: 16, color: AppColors.violet500),
                              Text('${summary.streak} j', style: AppTextStyles.caption.copyWith(color: AppColors.violet700)),
                              const SizedBox(width: AppSpacing.sm),
                            ],
                            Text('Journal', style: AppTextStyles.label.copyWith(color: AppColors.violet700)),
                            const Icon(Icons.chevron_right_rounded, color: AppColors.violet700, size: 20),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.md),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      for (final level in MoodLevel.values)
                        Pressable(
                          semanticLabel: 'Je me sens : ${level.label}',
                          onTap: () => MoodCheckInSheet.show(level),
                          child: MoodFace(level: level, selected: today?.level == level),
                        ),
                    ],
                  ),
                ],
              ),
            ),
          ),
          if (summary.suggestion != MoodSuggestion.none) ...[
            const SizedBox(height: AppSpacing.md),
            MoodSuggestionCard(suggestion: summary.suggestion, onTalk: onTalk),
          ],
        ],
      );
    });
  }
}
