import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:just_audio/just_audio.dart';

import '../../../../core/error/failure.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/app_toast.dart';
import '../../domain/entities/exercise.dart';
import '../controllers/exercises_controller.dart';

/// Voix guidée : lecture en ligne, ou depuis le téléphone une fois téléchargée.
class ExerciseAudioBar extends StatefulWidget {
  const ExerciseAudioBar({super.key, required this.exercise});

  final Exercise exercise;

  @override
  State<ExerciseAudioBar> createState() => _ExerciseAudioBarState();
}

class _ExerciseAudioBarState extends State<ExerciseAudioBar> {
  final _controller = Get.find<ExercisesController>();
  final _player = AudioPlayer();
  String? _localPath;
  double? _download;
  bool _ready = false;

  @override
  void initState() {
    super.initState();
    _prepare();
  }

  Future<void> _prepare() async {
    _localPath = await _controller.offlineAudio(widget.exercise);
    try {
      _localPath != null ? await _player.setFilePath(_localPath!) : await _player.setUrl(widget.exercise.audioUrl!);
      if (mounted) {
        setState(() => _ready = true);
      }
    } on PlayerException {
      // Hors ligne et pas encore téléchargé : la consigne écrite suffit.
    }
  }

  Future<void> _downloadAudio() async {
    setState(() => _download = 0);
    try {
      _localPath = await _controller.downloadAudio(widget.exercise, onProgress: (p) => mounted ? setState(() => _download = p) : null);
      AppToast.show('Téléchargé', 'Cet exercice est disponible sans connexion.');
    } on Failure catch (failure) {
      AppToast.show('Téléchargement impossible', failure.message);
    } finally {
      if (mounted) {
        setState(() => _download = null);
      }
    }
  }

  @override
  void dispose() {
    unawaited(_player.dispose());
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: AppSpacing.sm),
      decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
      child: Row(
        children: [
          StreamBuilder<PlayerState>(
            stream: _player.playerStateStream,
            builder: (_, snapshot) {
              final playing = snapshot.data?.playing ?? false;
              return IconButton(
                tooltip: playing ? 'Mettre la voix en pause' : 'Écouter la voix guidée',
                onPressed: !_ready ? null : (playing ? _player.pause : _player.play),
                icon: Icon(
                  playing ? Icons.pause_circle_filled_rounded : Icons.play_circle_fill_rounded,
                  color: AppColors.violet700,
                  size: 36,
                ),
              );
            },
          ),
          Expanded(
            child: StreamBuilder<Duration>(
              stream: _player.positionStream,
              builder: (_, snapshot) {
                final total = _player.duration ?? Duration.zero;
                final position = snapshot.data ?? Duration.zero;
                return Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Text('Voix guidée', style: AppTextStyles.label),
                    const SizedBox(height: AppSpacing.xs),
                    LinearProgressIndicator(
                      value: total.inMilliseconds == 0 ? 0 : position.inMilliseconds / total.inMilliseconds,
                      minHeight: 4,
                      borderRadius: BorderRadius.circular(AppSpacing.radiusPill),
                      color: AppColors.violet700,
                      backgroundColor: AppColors.violet100,
                    ),
                  ],
                );
              },
            ),
          ),
          const SizedBox(width: AppSpacing.sm),
          if (_download != null)
            SizedBox.square(dimension: 28, child: CircularProgressIndicator(value: _download == 0 ? null : _download, strokeWidth: 3))
          else if (_localPath != null)
            const Tooltip(
              message: 'Disponible hors ligne',
              child: Icon(Icons.offline_pin_rounded, color: AppColors.success),
            )
          else
            IconButton(
              tooltip: 'Télécharger pour l\'écouter hors ligne',
              onPressed: _downloadAudio,
              icon: const Icon(Icons.download_rounded, color: AppColors.violet700),
            ),
        ],
      ),
    );
  }
}
