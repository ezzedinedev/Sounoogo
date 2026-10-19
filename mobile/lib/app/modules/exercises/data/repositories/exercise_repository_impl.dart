import 'package:dio/dio.dart';

import '../../../../core/error/failure.dart';
import '../../../../core/error/failure_mapper.dart';
import '../../../../core/storage/json_cache.dart';
import '../../domain/entities/exercise.dart';
import '../../domain/entities/exercise_library.dart';
import '../../domain/repositories/exercise_repository.dart';
import '../datasources/exercise_audio_data_source.dart';
import '../datasources/exercise_remote_data_source.dart';
import '../models/exercise_model.dart';

class ExerciseRepositoryImpl implements ExerciseRepository {
  ExerciseRepositoryImpl(this._remote, this._cache, this._audio);

  static const _cacheKey = 'exercises';

  final ExerciseRemoteDataSource _remote;
  final JsonCache _cache;
  final ExerciseAudioDataSource _audio;

  @override
  Future<ExerciseLibrary> library() async {
    try {
      final json = await guardApi(_remote.fetch);
      await _cache.write(_cacheKey, json);
      return ExerciseLibrary(exercises: json.map(ExerciseModel.fromJson).toList());
    } on NetworkFailure {
      final cached = await _cache.read(_cacheKey);
      if (cached is! List) {
        rethrow;
      }
      return ExerciseLibrary(exercises: cached.cast<Map<String, dynamic>>().map(ExerciseModel.fromJson).toList(), fromCache: true);
    }
  }

  @override
  Future<String?> offlineAudio(Exercise exercise) async =>
      exercise.audioUrl == null ? null : _audio.existing(exercise.slug, exercise.audioUrl!);

  @override
  Future<String> downloadAudio(Exercise exercise, {void Function(double progress)? onProgress}) async {
    try {
      return await _audio.download(exercise.slug, exercise.audioUrl!, onProgress: onProgress);
    } on DioException {
      throw const NetworkFailure('Téléchargement impossible. Vérifie ta connexion et réessaie.');
    }
  }
}
