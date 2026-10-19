import '../entities/course.dart';
import '../repositories/course_repository.dart';

final class GetCourse {
  const GetCourse(this._repository);

  final CourseRepository _repository;

  Future<Course> call(String slug) => _repository.getBySlug(slug);
}
