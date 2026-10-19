import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:just_audio/just_audio.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/message.dart';
import '../controllers/listening_controller.dart';

/// Lecteur d'un message vocal : le fichier n'est téléchargé qu'à la première écoute.
class VoiceBubble extends StatefulWidget {
  const VoiceBubble({super.key, required this.message, required this.mine});

  final Message message;
  final bool mine;

  @override
  State<VoiceBubble> createState() => _VoiceBubbleState();
}

class _VoiceBubbleState extends State<VoiceBubble> {
  final _player = AudioPlayer();
  bool _loading = false;
  bool _loaded = false;

  Color get _foreground => widget.mine ? AppColors.white : AppColors.violet700;

  Future<void> _toggle() async {
    if (_player.playing) {
      await _player.pause();
      return;
    }
    if (!_loaded) {
      setState(() => _loading = true);
      try {
        await _player.setFilePath(await Get.find<ListeningController>().voiceFile(widget.message));
        _loaded = true;
      } catch (_) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Lecture impossible pour le moment.')));
        }
        return;
      } finally {
        if (mounted) {
          setState(() => _loading = false);
        }
      }
    }
    if (_player.processingState == ProcessingState.completed) {
      await _player.seek(Duration.zero);
    }
    unawaited(_player.play());
  }

  @override
  void dispose() {
    unawaited(_player.dispose());
    super.dispose();
  }

  String _format(Duration d) => '${d.inMinutes}:${(d.inSeconds % 60).toString().padLeft(2, '0')}';

  @override
  Widget build(BuildContext context) {
    final total = Duration(seconds: widget.message.audioDuration ?? 0);

    return SizedBox(
      width: 220,
      child: Row(
        children: [
          StreamBuilder<bool>(
            stream: _player.playingStream,
            builder: (_, snapshot) => IconButton(
              tooltip: snapshot.data ?? false ? 'Pause' : 'Écouter le message vocal',
              onPressed: _loading ? null : _toggle,
              icon: _loading
                  ? SizedBox.square(dimension: 22, child: CircularProgressIndicator(strokeWidth: 2.4, color: _foreground))
                  : Icon(snapshot.data ?? false ? Icons.pause_rounded : Icons.play_arrow_rounded, color: _foreground, size: 30),
            ),
          ),
          Expanded(
            child: StreamBuilder<Duration>(
              stream: _player.positionStream,
              builder: (_, snapshot) {
                final position = snapshot.data ?? Duration.zero;
                final progress = total.inMilliseconds == 0 ? 0.0 : (position.inMilliseconds / total.inMilliseconds).clamp(0.0, 1.0);
                return Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    _Waveform(progress: progress, color: _foreground, seed: widget.message.id),
                    const SizedBox(height: AppSpacing.xs),
                    Text(
                      _format(position > Duration.zero ? position : total),
                      style: AppTextStyles.caption.copyWith(
                        fontSize: 11,
                        color: widget.mine ? AppColors.white.withValues(alpha: 0.85) : AppColors.textSecondary,
                      ),
                    ),
                  ],
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

/// Forme d'onde stylisée (stable pour un même message) qui se colore à la lecture.
class _Waveform extends StatelessWidget {
  const _Waveform({required this.progress, required this.color, required this.seed});

  final double progress;
  final Color color;
  final int seed;

  static const _bars = 26;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 22,
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          for (var i = 0; i < _bars; i++)
            Expanded(
              child: Center(
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 150),
                  width: 2.5,
                  height: 5 + 16 * (((i * 7 + seed * 13) % 11) / 10),
                  decoration: BoxDecoration(
                    color: i / _bars <= progress ? color : color.withValues(alpha: 0.35),
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }
}
