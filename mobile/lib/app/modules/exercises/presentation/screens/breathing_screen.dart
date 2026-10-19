import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/exercise.dart';
import '../widgets/breathing_session.dart';

/// Respiration apaisante directe (depuis le SOS ou une suggestion du journal d'humeur), sans réseau.
class BreathingScreen extends StatelessWidget {
  const BreathingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.violet950,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        foregroundColor: AppColors.white,
        titleTextStyle: AppTextStyles.subtitle.copyWith(color: AppColors.white),
        title: const Text('Respirer'),
      ),
      body: const SafeArea(
        child: Padding(
          padding: EdgeInsets.all(AppSpacing.page),
          child: BreathingSession(pattern: BreathingPattern.calming),
        ),
      ),
    );
  }
}
