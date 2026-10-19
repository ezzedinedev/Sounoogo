import '../entities/course.dart';
import '../repositories/course_repository.dart';

final class GetCourses {
  const GetCourses(this._repository);

  final CourseRepository _repository;

  Future<List<Course>> call() => _repository.getAll();
}
