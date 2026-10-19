import 'dart:io';

import 'package:dio/dio.dart';
import 'package:path_provider/path_provider.dart';

/// Fichiers audio des exercices, rangés dans le dossier privé de l'app.
class ExerciseAudioDataSource {
  ExerciseAudioDataSource({Dio? dio}) : _dio = dio ?? Dio();

  final Dio _dio;

  Future<File> _file(String slug, String url) async {
    final directory = Directory('${(await getApplicationSupportDirectory()).path}/exercises');
    await directory.create(recursive: true);
    final extension = Uri.parse(url).path.split('.').last;
    return File('${directory.path}/$slug.${extension.length <= 4 ? extension : 'mp3'}');
  }

  Future<String?> existing(String slug, String url) async {
    final file = await _file(slug, url);
    return await file.exists() ? file.path : null;
  }

  Future<String> download(String slug, String url, {void Function(double progress)? onProgress}) async {
    final file = await _file(slug, url);
    final partial = File('${file.path}.part');
    await _dio.download(url, partial.path, onReceiveProgress: (received, total) => total > 0 ? onProgress?.call(received / total) : null);
    await partial.rename(file.path);
    return file.path;
  }
}
