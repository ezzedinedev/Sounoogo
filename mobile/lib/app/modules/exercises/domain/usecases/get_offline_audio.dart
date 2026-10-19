import '../entities/exercise.dart';
import '../repositories/exercise_repository.dart';

final class GetOfflineAudio {
  const GetOfflineAudio(this._repository);

  final ExerciseRepository _repository;

  Future<String?> call(Exercise exercise) async => exercise.audioUrl == null ? null : _repository.offlineAudio(exercise);
}
