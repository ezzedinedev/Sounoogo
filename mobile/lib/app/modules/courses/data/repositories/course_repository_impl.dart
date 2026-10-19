import 'dart:io';

import 'package:path_provider/path_provider.dart';

import '../../../../core/error/failure_mapper.dart';
import '../../domain/entities/certificate.dart';
import '../../domain/entities/course.dart';
import '../../domain/entities/enrollment.dart';
import '../../domain/entities/learning_progress.dart';
import '../../domain/entities/lesson.dart';
import '../../domain/entities/lesson_completion.dart';
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
  Future<LessonCompletion> completeLesson(String slug, int lessonId) => guardApi(() => _remote.complete(slug, lessonId));

  @override
  Future<LearningProgress> progress() => guardApi(_remote.progress);

  @override
  Future<Certificate> issueCertificate(String slug, String holderName) => guardApi(() => _remote.issueCertificate(slug, holderName));

  @override
  Future<String> certificateFile(Certificate certificate) async {
    // Fichier temporaire : l'attestation n'est conservée que si la personne choisit de la partager ou l'enregistrer.
    final directory = Directory('${(await getTemporaryDirectory()).path}/certificates');
    await directory.create(recursive: true);
    final file = File('${directory.path}/attestation-sunoogo-${certificate.courseSlug}.pdf');
    if (!await file.exists()) {
      await guardApi(() => _remote.download(certificate.pdfUrl, file.path));
    }
    return file.path;
  }
}
