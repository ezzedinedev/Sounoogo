import '../entities/lesson.dart';
import '../repositories/course_repository.dart';

final class GetLesson {
  const GetLesson(this._repository);

  final CourseRepository _repository;

  Future<Lesson> call(String slug, int lessonId) => _repository.lesson(slug, lessonId);
}
