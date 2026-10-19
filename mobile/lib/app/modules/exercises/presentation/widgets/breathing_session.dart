import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/exercise.dart';

enum _Phase {
  inhale('Inspire'),
  hold('Garde l\'air'),
  exhale('Expire doucement'),
  rest('Pause');

  const _Phase(this.label);

  final String label;
}

/// Respiration guidée : le cercle grandit à l'inspiration et se resserre à l'expiration.
/// Le cercle reste immobile si les animations sont réduites ; le texte guide alors seul.
class BreathingSession extends StatefulWidget {
  const BreathingSession({super.key, required this.pattern, this.onFinished});

  final BreathingPattern pattern;
  final VoidCallback? onFinished;

  @override
  State<BreathingSession> createState() => _BreathingSessionState();
}

class _BreathingSessionState extends State<BreathingSession> with SingleTickerProviderStateMixin {
  late final AnimationController _circle = AnimationController(vsync: this);
  Timer? _timer;
  _Phase _phase = _Phase.inhale;
  int _cycle = 0;
  bool _running = false;

  BreathingPattern get _pattern => widget.pattern;

  @override
  void dispose() {
    _timer?.cancel();
    _circle.dispose();
    super.dispose();
  }

  int _seconds(_Phase phase) => switch (phase) {
    _Phase.inhale => _pattern.inhale,
    _Phase.hold => _pattern.hold,
    _Phase.exhale => _pattern.exhale,
    _Phase.rest => _pattern.rest,
  };

  _Phase? _next(_Phase phase) {
    for (final candidate in _Phase.values.skip(phase.index + 1)) {
      if (_seconds(candidate) > 0) {
        return candidate;
      }
    }
    return null;
  }

  void _start() {
    setState(() {
      _running = true;
      _cycle = 0;
    });
    _enter(_Phase.inhale);
  }

  void _stop() {
    _timer?.cancel();
    _circle.stop();
    setState(() => _running = false);
  }

  void _enter(_Phase phase) {
    HapticFeedback.selectionClick();
    setState(() => _phase = phase);
    final duration = Duration(seconds: _seconds(phase));
    switch (phase) {
      case _Phase.inhale:
        _circle.animateTo(1, duration: duration, curve: Curves.easeInOut);
      case _Phase.exhale:
        _circle.animateTo(0, duration: duration, curve: Curves.easeInOut);
      case _Phase.hold:
      case _Phase.rest:
        break;
    }
    _timer = Timer(duration, () {
      if (!mounted) {
        return;
      }
      final next = _next(phase);
      if (next != null) {
        _enter(next);
      } else if (++_cycle >= _pattern.cycles) {
        _stop();
        HapticFeedback.mediumImpact();
        widget.onFinished?.call();
      } else {
        _enter(_Phase.inhale);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final reduceMotion = MediaQuery.disableAnimationsOf(context);
    final minutes = (_pattern.total.inSeconds / 60).ceil();

    return Column(
      children: [
        Text(
          _running
              ? 'Cycle ${_cycle + 1} sur ${_pattern.cycles}'
              : 'Environ $minutes minute${minutes > 1 ? 's' : ''} pour faire redescendre la tension.',
          style: AppTextStyles.body.copyWith(color: AppColors.lilac200),
          textAlign: TextAlign.center,
        ),
        Expanded(
          child: Center(
            child: AnimatedBuilder(
              animation: _circle,
              builder: (context, _) {
                final t = reduceMotion ? 0.6 : _circle.value;
                final size = 140 + 140 * t;
                return Stack(
                  alignment: Alignment.center,
                  children: [
                    Container(
                      width: size + 40,
                      height: size + 40,
                      decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.violet300.withValues(alpha: 0.08)),
                    ),
                    Container(
                      width: size * 0.82,
                      height: size * 0.82,
                      decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.violet300.withValues(alpha: 0.14)),
                    ),
                    Container(
                      width: size * 0.64,
                      height: size * 0.64,
                      decoration: const BoxDecoration(shape: BoxShape.circle, gradient: AppColors.brandGradient),
                    ),
                    Semantics(
                      liveRegion: true,
                      child: Text(
                        _running ? _phase.label : 'Prêt·e ?',
                        style: AppTextStyles.titleAccent.copyWith(color: AppColors.white, fontSize: 28),
                        textAlign: TextAlign.center,
                      ),
                    ),
                  ],
                );
              },
            ),
          ),
        ),
        Padding(
          padding: const EdgeInsets.only(bottom: AppSpacing.md),
          child: Text(
            [
              'Inspire ${_pattern.inhale} s',
              if (_pattern.hold > 0) 'garde ${_pattern.hold} s',
              'expire ${_pattern.exhale} s',
              if (_pattern.rest > 0) 'pause ${_pattern.rest} s',
            ].join(' · '),
            style: AppTextStyles.caption.copyWith(color: AppColors.lilac200),
          ),
        ),
        FilledButton(
          onPressed: _running ? _stop : _start,
          style: FilledButton.styleFrom(backgroundColor: AppColors.white, foregroundColor: AppColors.violet800),
          child: Text(_running ? 'Arrêter' : 'Commencer'),
        ),
      ],
    );
  }
}
