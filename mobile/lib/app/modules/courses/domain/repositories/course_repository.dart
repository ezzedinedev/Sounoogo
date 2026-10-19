import '../entities/course.dart';
import '../entities/enrollment.dart';
import '../entities/lesson.dart';

abstract interface class CourseRepository {
  Future<List<Course>> getAll();

  Future<Course> getBySlug(String slug);

  Future<List<Enrollment>> enrollments();

  /// Lève une `PaymentRequiredFailure` si la formation est payante et non couverte par l'abonnement.
  Future<Enrollment> enroll(String slug);

  Future<Lesson> lesson(String slug, int lessonId);

  Future<Enrollment> completeLesson(String slug, int lessonId);
}
