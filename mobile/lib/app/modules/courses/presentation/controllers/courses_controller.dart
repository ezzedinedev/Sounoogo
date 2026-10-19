import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../domain/entities/course.dart';
import '../../domain/entities/enrollment.dart';
import '../../domain/usecases/get_courses.dart';
import '../../domain/usecases/get_enrollments.dart';

class CoursesController extends GetxController {
  CoursesController({required this._getCourses, required this._getEnrollments});

  final GetCourses _getCourses;
  final GetEnrollments _getEnrollments;

  final items = <Course>[].obs;
  final enrollments = <String, Enrollment>{}.obs;
  final isLoading = false.obs;
  final errorMessage = RxnString();

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      final courses = _getCourses();
      final enrolled = _getEnrollments();
      await Future.wait([courses, enrolled]);
      items.assignAll(await courses);
      enrollments.assignAll({for (final enrollment in await enrolled) enrollment.courseSlug: enrollment});
    } on Failure catch (failure) {
      errorMessage.value = failure is NetworkFailure ? 'Impossible de charger les formations.' : failure.message;
    } finally {
      isLoading.value = false;
    }
  }
}
