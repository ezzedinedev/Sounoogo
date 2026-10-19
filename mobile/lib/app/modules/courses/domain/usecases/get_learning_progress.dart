import '../entities/learning_progress.dart';
import '../repositories/course_repository.dart';

final class GetLearningProgress {
  const GetLearningProgress(this._repository);

  final CourseRepository _repository;

  Future<LearningProgress> call() => _repository.progress();
}
