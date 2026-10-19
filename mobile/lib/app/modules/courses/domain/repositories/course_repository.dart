import '../entities/certificate.dart';
import '../entities/course.dart';
import '../entities/enrollment.dart';
import '../entities/learning_progress.dart';
import '../entities/lesson.dart';
import '../entities/lesson_completion.dart';

abstract interface class CourseRepository {
  Future<List<Course>> getAll();

  Future<Course> getBySlug(String slug);

  Future<List<Enrollment>> enrollments();

  /// Lève une `PaymentRequiredFailure` si la formation est payante et non couverte par l'abonnement.
  Future<Enrollment> enroll(String slug);

  Future<Lesson> lesson(String slug, int lessonId);

  Future<LessonCompletion> completeLesson(String slug, int lessonId);

  Future<LearningProgress> progress();

  /// Échoue (409) tant que la formation n'est pas terminée. Une seule attestation par formation.
  Future<Certificate> issueCertificate(String slug, String holderName);

  /// Télécharge le PDF de l'attestation et renvoie le chemin du fichier local.
  Future<String> certificateFile(Certificate certificate);
}
