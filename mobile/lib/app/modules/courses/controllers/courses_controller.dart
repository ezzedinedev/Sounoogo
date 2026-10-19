import 'package:get/get.dart';

import '../../../core/network/api_exception.dart';
import '../models/course_model.dart';
import '../repositories/course_repository.dart';

class CoursesController extends GetxController {
  CoursesController(this._repository);

  final CourseRepository _repository;

  final items = <CourseModel>[].obs;
  final enrollments = <String, EnrollmentModel>{}.obs;
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
      final results = await Future.wait([_repository.getAll(), _repository.enrollments()]);
      items.assignAll(results[0] as List<CourseModel>);
      enrollments.assignAll({for (final e in results[1] as List<EnrollmentModel>) e.courseSlug: e});
    } on ApiException catch (error) {
      errorMessage.value = error.isNetwork ? 'Impossible de charger les formations.' : error.message;
    } finally {
      isLoading.value = false;
    }
  }
}
