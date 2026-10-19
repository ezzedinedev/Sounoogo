import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../../../../core/media/voice_recorder.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/usecases/send_voice_message.dart';

/// Barre d'enregistrement : durée, niveau sonore animé, annuler ou envoyer. S'arrête seule à 2 minutes.
class VoiceRecorderBar extends StatefulWidget {
  const VoiceRecorderBar({super.key, required this.onCancel, required this.onSend});

  final VoidCallback onCancel;
  final Future<void> Function(String path, Duration length) onSend;

  @override
  State<VoiceRecorderBar> createState() => _VoiceRecorderBarState();
}

class _VoiceRecorderBarState extends State<VoiceRecorderBar> {
  final _recorder = VoiceRecorder();
  final _stopwatch = Stopwatch();
  final _levels = List<double>.filled(24, 0.1, growable: true);
  StreamSubscription<double>? _levelSub;
  Timer? _ticker;
  bool _ready = false;

  @override
  void initState() {
    super.initState();
    _begin();
  }

  Future<void> _begin() async {
    if (!await _recorder.requestPermission()) {
      widget.onCancel();
      return;
    }
    await _recorder.start();
    HapticFeedback.mediumImpact();
    _stopwatch.start();
    _levelSub = _recorder.levels().listen(
      (level) => setState(
        () => _levels
          ..removeAt(0)
          ..add(level),
      ),
    );
    _ticker = Timer.periodic(const Duration(milliseconds: 250), (_) {
      if (_stopwatch.elapsed.inSeconds >= SendVoiceMessage.maxSeconds) {
        _send();
      } else {
        setState(() {});
      }
    });
    setState(() => _ready = true);
  }

  Future<void> _send() async {
    _ticker?.cancel();
    _stopwatch.stop();
    final path = await _recorder.stop();
    if (path != null) {
      await widget.onSend(path, _stopwatch.elapsed);
    }
  }

  Future<void> _cancel() async {
    _ticker?.cancel();
    await _recorder.cancel();
    widget.onCancel();
  }

  @override
  void dispose() {
    _ticker?.cancel();
    _levelSub?.cancel();
    _recorder.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final elapsed = _stopwatch.elapsed;
    final label = '${elapsed.inMinutes}:${(elapsed.inSeconds % 60).toString().padLeft(2, '0')}';

    return Row(
      children: [
        IconButton(
          tooltip: 'Annuler l\'enregistrement',
          onPressed: _cancel,
          icon: const Icon(Icons.delete_outline_rounded, color: AppColors.sos),
        ),
        const _RecordingDot(),
        const SizedBox(width: AppSpacing.sm),
        Text(label, style: AppTextStyles.label.copyWith(fontFeatures: const [FontFeature.tabularFigures()])),
        const SizedBox(width: AppSpacing.md),
        Expanded(
          child: SizedBox(
            height: 28,
            child: Row(
              children: [
                for (final level in _levels)
                  Expanded(
                    child: Center(
                      child: AnimatedContainer(
                        duration: const Duration(milliseconds: 120),
                        width: 3,
                        height: 4 + 22 * level,
                        decoration: BoxDecoration(color: AppColors.violet500, borderRadius: BorderRadius.circular(2)),
                      ),
                    ),
                  ),
              ],
            ),
          ),
        ),
        const SizedBox(width: AppSpacing.sm),
        IconButton.filled(
          tooltip: 'Envoyer le message vocal',
          onPressed: _ready ? _send : null,
          style: IconButton.styleFrom(backgroundColor: AppColors.violet700, foregroundColor: AppColors.white),
          icon: const Icon(Icons.arrow_upward_rounded),
        ),
      ],
    );
  }
}

class _RecordingDot extends StatefulWidget {
  const _RecordingDot();

  @override
  State<_RecordingDot> createState() => _RecordingDotState();
}

class _RecordingDotState extends State<_RecordingDot> with SingleTickerProviderStateMixin {
  late final AnimationController _blink = AnimationController(vsync: this, duration: const Duration(milliseconds: 900))
    ..repeat(reverse: true);

  @override
  void dispose() {
    _blink.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return FadeTransition(
      opacity: Tween(begin: 0.3, end: 1.0).animate(_blink),
      child: Container(
        width: 10,
        height: 10,
        decoration: const BoxDecoration(color: AppColors.sos, shape: BoxShape.circle),
      ),
    );
  }
}
