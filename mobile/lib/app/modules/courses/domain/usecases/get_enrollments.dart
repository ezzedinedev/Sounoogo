import '../entities/enrollment.dart';
import '../repositories/course_repository.dart';

final class GetEnrollments {
  const GetEnrollments(this._repository);

  final CourseRepository _repository;

  Future<List<Enrollment>> call() => _repository.enrollments();

  /// Inscription du membre à une formation donnée, s'il y en a une.
  Future<Enrollment?> forCourse(String slug) async => (await call()).where((e) => e.courseSlug == slug).firstOrNull;
}
