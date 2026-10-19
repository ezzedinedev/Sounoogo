import '../entities/enrollment.dart';
import '../repositories/course_repository.dart';

final class EnrollInCourse {
  const EnrollInCourse(this._repository);

  final CourseRepository _repository;

  Future<Enrollment> call(String slug) => _repository.enroll(slug);
}
