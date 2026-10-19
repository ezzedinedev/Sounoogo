import '../entities/enrollment.dart';
import '../repositories/course_repository.dart';

final class CompleteLesson {
  const CompleteLesson(this._repository);

  final CourseRepository _repository;

  Future<Enrollment> call(String slug, int lessonId) => _repository.completeLesson(slug, lessonId);
}
