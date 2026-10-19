import 'dart:async';
import 'dart:io';

import 'package:path_provider/path_provider.dart';
import 'package:record/record.dart';

/// Enregistrement d'un message vocal dans un fichier temporaire (AAC).
class VoiceRecorder {
  VoiceRecorder({AudioRecorder? recorder}) : _recorder = recorder ?? AudioRecorder();

  final AudioRecorder _recorder;

  /// Demande l'accès au micro si besoin.
  Future<bool> requestPermission() => _recorder.hasPermission();

  Future<void> start() async {
    final directory = await getTemporaryDirectory();
    final path = '${directory.path}/voice-${DateTime.now().millisecondsSinceEpoch}.m4a';
    await _recorder.start(const RecordConfig(encoder: AudioEncoder.aacLc, bitRate: 64000, sampleRate: 22050, numChannels: 1), path: path);
  }

  /// Niveau sonore entre 0 et 1, pour animer l'interface.
  Stream<double> levels() =>
      _recorder.onAmplitudeChanged(const Duration(milliseconds: 120)).map((amplitude) => ((amplitude.current + 50) / 50).clamp(0.0, 1.0));

  /// Fin de l'enregistrement : chemin du fichier.
  Future<String?> stop() => _recorder.stop();

  Future<void> cancel() async {
    final path = await _recorder.stop();
    if (path != null) {
      unawaited(File(path).delete().catchError((Object _) => File(path)));
    }
  }

  Future<void> dispose() => _recorder.dispose();
}
