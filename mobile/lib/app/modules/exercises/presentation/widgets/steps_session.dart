import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/exercise.dart';

/// Lecteur d'étapes : une consigne à la fois, un anneau qui se remplit, passage automatique à la suivante.
class StepsSession extends StatefulWidget {
  const StepsSession({super.key, required this.steps, this.onFinished});

  final List<ExerciseStep> steps;
  final VoidCallback? onFinished;

  @override
  State<StepsSession> createState() => _StepsSessionState();
}

class _StepsSessionState extends State<StepsSession> with SingleTickerProviderStateMixin {
  late final AnimationController _progress = AnimationController(vsync: this)..addStatusListener(_onStepDone);
  int _index = 0;
  bool _started = false;

  ExerciseStep get _step => widget.steps[_index];
  bool get _playing => _progress.isAnimating;

  @override
  void dispose() {
    _progress.dispose();
    super.dispose();
  }

  void _onStepDone(AnimationStatus status) {
    if (status != AnimationStatus.completed) {
      return;
    }
    if (_index < widget.steps.length - 1) {
      _go(_index + 1);
    } else {
      HapticFeedback.mediumImpact();
      widget.onFinished?.call();
    }
  }

  void _go(int index) {
    HapticFeedback.selectionClick();
    setState(() {
      _index = index;
      _started = true;
    });
    _progress
      ..duration = Duration(seconds: widget.steps[index].seconds)
      ..forward(from: 0);
  }

  void _toggle() {
    if (!_started) {
      _go(0);
    } else if (_playing) {
      _progress.stop();
      setState(() {});
    } else {
      _progress.forward();
      setState(() {});
    }
  }

  @override
  Widget build(BuildContext context) {
    final total = widget.steps.length;

    return Column(
      children: [
        // Points de progression : un par étape.
        Row(
          children: [
            for (var i = 0; i < total; i++)
              Expanded(
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 300),
                  height: 4,
                  margin: const EdgeInsets.symmetric(horizontal: 2),
                  decoration: BoxDecoration(
                    color: i < _index || (i == _index && _started) ? AppColors.violet700 : AppColors.violet100,
                    borderRadius: BorderRadius.circular(AppSpacing.radiusPill),
                  ),
                ),
              ),
          ],
        ),
        const SizedBox(height: AppSpacing.md),
        Text(_started ? 'Étape ${_index + 1} sur $total' : '$total étapes, à ton rythme', style: AppTextStyles.caption),
        Expanded(
          child: Center(
            child: AnimatedSwitcher(
              duration: const Duration(milliseconds: 380),
              transitionBuilder: (child, animation) => FadeTransition(
                opacity: animation,
                child: SlideTransition(
                  position: Tween(begin: const Offset(0, 0.08), end: Offset.zero).animate(animation),
                  child: child,
                ),
              ),
              child: Padding(
                key: ValueKey(_started ? _index : -1),
                padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
                child: Semantics(
                  liveRegion: true,
                  child: Text(
                    _started ? _step.text : 'Installe-toi confortablement. Quand tu es prêt·e, lance l\'exercice.',
                    style: AppTextStyles.title.copyWith(fontWeight: FontWeight.w500, height: 1.35),
                    textAlign: TextAlign.center,
                  ),
                ),
              ),
            ),
          ),
        ),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            IconButton(
              tooltip: 'Étape précédente',
              onPressed: _started && _index > 0 ? () => _go(_index - 1) : null,
              icon: const Icon(Icons.skip_previous_rounded, size: 32),
            ),
            const SizedBox(width: AppSpacing.lg),
            SizedBox.square(
              dimension: 88,
              child: Stack(
                alignment: Alignment.center,
                children: [
                  AnimatedBuilder(
                    animation: _progress,
                    builder: (_, _) => SizedBox.square(
                      dimension: 88,
                      child: CircularProgressIndicator(
                        value: _started ? _progress.value : 0,
                        strokeWidth: 4,
                        color: AppColors.violet700,
                        backgroundColor: AppColors.violet100,
                      ),
                    ),
                  ),
                  IconButton.filled(
                    tooltip: _playing ? 'Pause' : 'Lecture',
                    onPressed: _toggle,
                    iconSize: 34,
                    style: IconButton.styleFrom(
                      backgroundColor: AppColors.violet700,
                      foregroundColor: AppColors.white,
                      minimumSize: const Size.square(68),
                    ),
                    icon: Icon(_playing ? Icons.pause_rounded : Icons.play_arrow_rounded),
                  ),
                ],
              ),
            ),
            const SizedBox(width: AppSpacing.lg),
            IconButton(
              tooltip: 'Étape suivante',
              onPressed: _started && _index < total - 1 ? () => _go(_index + 1) : null,
              icon: const Icon(Icons.skip_next_rounded, size: 32),
            ),
          ],
        ),
      ],
    );
  }
}
