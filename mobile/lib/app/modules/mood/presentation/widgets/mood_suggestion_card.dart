import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/mood_summary.dart';

/// Suggestion bienveillante tirée du journal d'humeur.
class MoodSuggestionCard extends StatelessWidget {
  const MoodSuggestionCard({super.key, required this.suggestion, required this.onTalk});

  final MoodSuggestion suggestion;
  final VoidCallback onTalk;

  @override
  Widget build(BuildContext context) {
    final (icon, title, text, action, onTap) = switch (suggestion) {
      MoodSuggestion.talk => (
        Icons.forum_outlined,
        'Ces derniers jours ont l\'air lourds',
        'Tu n\'as pas à porter ça seul·e. Un écoutant peut t\'accompagner, anonymement.',
        'Parler maintenant',
        onTalk,
      ),
      MoodSuggestion.exercise => (
        Icons.air_rounded,
        'Prends deux minutes pour toi',
        'Une respiration guidée aide à faire redescendre la tension.',
        'Respirer avec moi',
        () => Get.toNamed(AppRoutes.breathing),
      ),
      MoodSuggestion.none => (null, '', '', '', null),
    };
    if (icon == null || onTap == null) {
      return const SizedBox.shrink();
    }

    return Container(
      padding: const EdgeInsets.all(AppSpacing.lg),
      decoration: BoxDecoration(
        color: AppColors.violet50,
        borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
        border: Border.all(color: AppColors.violet100),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: AppColors.violet700),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: AppTextStyles.label),
                const SizedBox(height: AppSpacing.xs),
                Text(text, style: AppTextStyles.caption),
                const SizedBox(height: AppSpacing.sm),
                TextButton(
                  onPressed: onTap,
                  style: TextButton.styleFrom(padding: EdgeInsets.zero, minimumSize: const Size(0, 36)),
                  child: Text(action),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
