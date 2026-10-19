import '../entities/exercise_library.dart';
import '../repositories/exercise_repository.dart';

final class GetExercises {
  const GetExercises(this._repository);

  final ExerciseRepository _repository;

  Future<ExerciseLibrary> call() => _repository.library();
}
