import '../../../../core/error/failure_mapper.dart';
import '../../domain/entities/course.dart';
import '../../domain/entities/enrollment.dart';
import '../../domain/entities/lesson.dart';
import '../../domain/repositories/course_repository.dart';
import '../datasources/course_remote_data_source.dart';

class CourseRepositoryImpl implements CourseRepository {
  CourseRepositoryImpl(this._remote);

  final CourseRemoteDataSource _remote;

  @override
  Future<List<Course>> getAll() => guardApi(_remote.getAll);

  @override
  Future<Course> getBySlug(String slug) => guardApi(() => _remote.getBySlug(slug));

  @override
  Future<List<Enrollment>> enrollments() => guardApi(_remote.enrollments);

  @override
  Future<Enrollment> enroll(String slug) => guardApi(() => _remote.enroll(slug));

  @override
  Future<Lesson> lesson(String slug, int lessonId) => guardApi(() => _remote.lesson(slug, lessonId));

  @override
  Future<Enrollment> completeLesson(String slug, int lessonId) => guardApi(() => _remote.complete(slug, lessonId));
}
