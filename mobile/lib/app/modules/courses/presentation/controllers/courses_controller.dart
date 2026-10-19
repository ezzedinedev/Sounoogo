import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../domain/entities/course.dart';
import '../../domain/entities/enrollment.dart';
import '../../domain/entities/learning_progress.dart';
import '../../domain/usecases/get_courses.dart';
import '../../domain/usecases/get_enrollments.dart';
import '../../domain/usecases/get_learning_progress.dart';

class CoursesController extends GetxController {
  CoursesController({required this._getCourses, required this._getEnrollments, required this._getLearningProgress});

  final GetCourses _getCourses;
  final GetEnrollments _getEnrollments;
  final GetLearningProgress _getLearningProgress;

  final items = <Course>[].obs;
  final progress = Rxn<LearningProgress>();
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
      _loadProgress();
    } on Failure catch (failure) {
      errorMessage.value = failure is NetworkFailure ? 'Impossible de charger les formations.' : failure.message;
    } finally {
      isLoading.value = false;
    }
  }

  /// Secondaire : en cas d'échec, l'onglet reste utilisable sans la carte de progression.
  Future<void> _loadProgress() async {
    try {
      progress.value = await _getLearningProgress();
    } on Failure {
      progress.value = null;
    }
  }
}
