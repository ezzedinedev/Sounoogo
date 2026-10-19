import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_provider.dart';
import '../models/course_model.dart';

class CourseRepository {
  CourseRepository(this._api);

  final ApiProvider _api;

  Future<List<CourseModel>> getAll() async {
    final response = await _api.get(ApiConstants.courses);
    return (response['data'] as List).map((c) => CourseModel.fromJson(c as Map<String, dynamic>)).toList();
  }

  Future<CourseModel> getBySlug(String slug) async =>
      CourseModel.fromJson((await _api.get(ApiConstants.course(slug)))['data'] as Map<String, dynamic>);

  Future<List<EnrollmentModel>> enrollments() async {
    final response = await _api.get(ApiConstants.enrollments);
    return (response['data'] as List).map((e) => EnrollmentModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<EnrollmentModel> enroll(String slug) async =>
      EnrollmentModel.fromJson((await _api.post(ApiConstants.courseEnroll(slug)))['data'] as Map<String, dynamic>);

  Future<LessonModel> lesson(String slug, int id) async =>
      LessonModel.fromJson((await _api.get(ApiConstants.lesson(slug, id)))['data'] as Map<String, dynamic>);

  Future<EnrollmentModel> complete(String slug, int id) async =>
      EnrollmentModel.fromJson((await _api.post(ApiConstants.lessonComplete(slug, id)))['data'] as Map<String, dynamic>);
}
