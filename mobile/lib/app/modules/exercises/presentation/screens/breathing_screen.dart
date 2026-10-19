import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';

/// Respiration guidée 4-2-6 : l'expiration plus longue que l'inspiration aide le corps à se calmer.
/// Fonctionne hors ligne ; le cercle reste immobile si l'utilisateur a réduit les animations.
class BreathingScreen extends StatefulWidget {
  const BreathingScreen({super.key});

  @override
  State<BreathingScreen> createState() => _BreathingScreenState();
}

enum _Phase {
  inhale('Inspire', 4),
  hold('Garde l\'air', 2),
  exhale('Expire doucement', 6);

  const _Phase(this.label, this.seconds);

  final String label;
  final int seconds;
}

class _BreathingScreenState extends State<BreathingScreen> with SingleTickerProviderStateMixin {
  static const _cycles = 8;

  late final AnimationController _circle = AnimationController(vsync: this);
  Timer? _timer;
  _Phase _phase = _Phase.inhale;
  int _cycle = 0;
  bool _running = false;

  int get _cycleSeconds => _Phase.values.fold(0, (total, phase) => total + phase.seconds);

  @override
  void dispose() {
    _timer?.cancel();
    _circle.dispose();
    super.dispose();
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
    final duration = Duration(seconds: phase.seconds);
    switch (phase) {
      case _Phase.inhale:
        _circle.animateTo(1, duration: duration, curve: Curves.easeInOut);
      case _Phase.hold:
        break;
      case _Phase.exhale:
        _circle.animateTo(0, duration: duration, curve: Curves.easeInOut);
    }
    _timer = Timer(duration, () {
      if (!mounted) {
        return;
      }
      switch (phase) {
        case _Phase.inhale:
          _enter(_Phase.hold);
        case _Phase.hold:
          _enter(_Phase.exhale);
        case _Phase.exhale:
          if (++_cycle >= _cycles) {
            _stop();
            HapticFeedback.mediumImpact();
          } else {
            _enter(_Phase.inhale);
          }
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final reduceMotion = MediaQuery.disableAnimationsOf(context);
    final minutes = (_cycles * _cycleSeconds / 60).round();

    return Scaffold(
      backgroundColor: AppColors.violet950,
      appBar: AppBar(backgroundColor: Colors.transparent, foregroundColor: AppColors.white, title: const Text('Respirer')),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.page),
          child: Column(
            children: [
              Text(
                _running ? 'Cycle ${_cycle + 1} sur $_cycles' : 'Environ $minutes minutes pour faire redescendre la tension.',
                style: AppTextStyles.body.copyWith(color: AppColors.lilac200),
                textAlign: TextAlign.center,
              ),
              Expanded(
                child: Center(
                  child: AnimatedBuilder(
                    animation: _circle,
                    builder: (context, _) {
                      final t = reduceMotion ? 0.6 : _circle.value;
                      return Stack(
                        alignment: Alignment.center,
                        children: [
                          for (final (scale, alpha) in [(1.0, 0.08), (0.82, 0.14), (0.64, 1.0)])
                            Container(
                              width: (140 + 140 * t) * scale + (scale == 1.0 ? 40 : 0),
                              height: (140 + 140 * t) * scale + (scale == 1.0 ? 40 : 0),
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                gradient: alpha == 1.0 ? AppColors.brandGradient : null,
                                color: alpha == 1.0 ? null : AppColors.violet300.withValues(alpha: alpha),
                              ),
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
              FilledButton(
                onPressed: _running ? _stop : _start,
                style: FilledButton.styleFrom(backgroundColor: AppColors.white, foregroundColor: AppColors.violet800),
                child: Text(_running ? 'Arrêter' : 'Commencer'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
