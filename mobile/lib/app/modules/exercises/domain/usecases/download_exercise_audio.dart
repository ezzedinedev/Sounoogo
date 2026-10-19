import '../entities/exercise.dart';
import '../repositories/exercise_repository.dart';

final class DownloadExerciseAudio {
  const DownloadExerciseAudio(this._repository);

  final ExerciseRepository _repository;

  Future<String> call(Exercise exercise, {void Function(double progress)? onProgress}) =>
      _repository.downloadAudio(exercise, onProgress: onProgress);
}
