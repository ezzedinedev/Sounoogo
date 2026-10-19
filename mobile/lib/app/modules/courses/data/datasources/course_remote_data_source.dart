import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';
import '../../domain/entities/certificate.dart';
import '../../domain/entities/course.dart';
import '../../domain/entities/enrollment.dart';
import '../../domain/entities/learning_progress.dart';
import '../../domain/entities/lesson.dart';
import '../../domain/entities/lesson_completion.dart';
import '../models/course_model.dart';

class CourseRemoteDataSource {
  CourseRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<List<Course>> getAll() async => _list(await _api.get(ApiConstants.courses), CourseModel.fromJson);

  Future<Course> getBySlug(String slug) async => CourseModel.fromJson(_data(await _api.get(ApiConstants.course(slug))));

  Future<List<Enrollment>> enrollments() async => _list(await _api.get(ApiConstants.enrollments), EnrollmentModel.fromJson);

  Future<Enrollment> enroll(String slug) async => EnrollmentModel.fromJson(_data(await _api.post(ApiConstants.courseEnroll(slug))));

  Future<Lesson> lesson(String slug, int id) async => LessonModel.fromJson(_data(await _api.get(ApiConstants.lesson(slug, id))));

  Future<LessonCompletion> complete(String slug, int id) async {
    final response = await _api.post(ApiConstants.lessonComplete(slug, id));
    return LessonCompletion(
      enrollment: EnrollmentModel.fromJson(_data(response)),
      newBadges: (response['new_badges'] as List? ?? const [])
          .map((badge) => LearningBadgeModel.fromJson(badge as Map<String, dynamic>))
          .toList(),
    );
  }

  Future<LearningProgress> progress() async => LearningProgressModel.fromJson(_data(await _api.get(ApiConstants.learningProgress)));

  Future<Certificate> issueCertificate(String slug, String holderName) async =>
      CertificateModel.fromJson(_data(await _api.post(ApiConstants.courseCertificate(slug), data: {'holder_name': holderName})));

  Future<void> download(String url, String savePath) => _api.download(url, savePath);

  Map<String, dynamic> _data(Map<String, dynamic> response) => response['data'] as Map<String, dynamic>;

  List<T> _list<T>(Map<String, dynamic> response, T Function(Map<String, dynamic>) parse) =>
      (response['data'] as List).map((item) => parse(item as Map<String, dynamic>)).toList();
}
