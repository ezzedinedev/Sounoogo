import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/state_views.dart';
import '../../domain/entities/exercise.dart';
import '../controllers/exercises_controller.dart';
import '../widgets/breathing_session.dart';
import '../widgets/exercise_audio_bar.dart';
import '../widgets/steps_session.dart';

class ExercisePlayerScreen extends StatefulWidget {
  const ExercisePlayerScreen({super.key});

  @override
  State<ExercisePlayerScreen> createState() => _ExercisePlayerScreenState();
}

class _ExercisePlayerScreenState extends State<ExercisePlayerScreen> {
  final _controller = Get.find<ExercisesController>();
  final _slug = Get.parameters['slug']!;
  bool _finished = false;
  int _run = 0;

  @override
  Widget build(BuildContext context) {
    return Obx(() {
      final exercise = _controller.find(_slug);
      if (exercise == null) {
        return Scaffold(
          appBar: AppBar(),
          body: _controller.isLoading.value
              ? const SkeletonList()
              : const EmptyState(icon: Icons.spa_outlined, title: 'Exercice introuvable'),
        );
      }
      final dark = exercise.isBreathing;

      return Scaffold(
        backgroundColor: dark ? AppColors.violet950 : AppColors.background,
        appBar: AppBar(
          backgroundColor: dark ? Colors.transparent : null,
          foregroundColor: dark ? AppColors.white : null,
          titleTextStyle: dark ? AppTextStyles.subtitle.copyWith(color: AppColors.white) : null,
          title: Text(exercise.title),
        ),
        body: SafeArea(
          child: Padding(
            padding: const EdgeInsets.all(AppSpacing.page),
            child: AnimatedSwitcher(
              duration: const Duration(milliseconds: 400),
              child: _finished
                  ? _Done(
                      dark: dark,
                      onAgain: () => setState(() {
                        _finished = false;
                        _run++;
                      }),
                    )
                  : Column(
                      key: ValueKey(_run),
                      children: [
                        if (!dark) ...[
                          Text(exercise.summary, style: AppTextStyles.bodySecondary, textAlign: TextAlign.center),
                          const SizedBox(height: AppSpacing.lg),
                        ],
                        if (exercise.audioUrl != null) ...[ExerciseAudioBar(exercise: exercise), const SizedBox(height: AppSpacing.lg)],
                        Expanded(child: _session(exercise)),
                      ],
                    ),
            ),
          ),
        ),
      );
    });
  }

  Widget _session(Exercise exercise) {
    void done() => setState(() => _finished = true);
    return exercise.isBreathing
        ? BreathingSession(pattern: exercise.breathing!, onFinished: done)
        : StepsSession(steps: exercise.steps, onFinished: done);
  }
}

class _Done extends StatelessWidget {
  const _Done({required this.dark, required this.onAgain});

  final bool dark;
  final VoidCallback onAgain;

  @override
  Widget build(BuildContext context) {
    final foreground = dark ? AppColors.white : AppColors.textPrimary;
    return Column(
      key: const ValueKey('done'),
      children: [
        const Spacer(),
        TweenAnimationBuilder<double>(
          tween: Tween(begin: 0.6, end: 1),
          duration: const Duration(milliseconds: 600),
          curve: Curves.elasticOut,
          builder: (_, scale, child) => Transform.scale(scale: scale, child: child),
          child: Container(
            width: 96,
            height: 96,
            decoration: const BoxDecoration(gradient: AppColors.brandGradient, shape: BoxShape.circle),
            child: const Icon(Icons.favorite_rounded, color: AppColors.white, size: 42),
          ),
        ),
        const SizedBox(height: AppSpacing.xl),
        Text('Bien joué', style: AppTextStyles.title.copyWith(color: foreground)),
        const SizedBox(height: AppSpacing.sm),
        Text(
          'Tu as pris ce moment pour toi. Reviens-y dès que tu en as besoin.',
          style: AppTextStyles.bodySecondary.copyWith(color: dark ? AppColors.lilac200 : null),
          textAlign: TextAlign.center,
        ),
        const Spacer(),
        OutlinedButton(
          onPressed: onAgain,
          style: dark
              ? OutlinedButton.styleFrom(
                  foregroundColor: AppColors.white,
                  side: const BorderSide(color: AppColors.lilac200),
                )
              : null,
          child: const Text('Recommencer'),
        ),
        const SizedBox(height: AppSpacing.md),
        FilledButton(onPressed: Get.back, child: const Text('Terminer')),
      ],
    );
  }
}
