import 'package:get/get.dart';

import '../../../core/network/api_exception.dart';
import '../models/course_model.dart';
import '../repositories/course_repository.dart';

class LessonController extends GetxController {
  LessonController(this._repository);

  final CourseRepository _repository;

  final lesson = Rxn<LessonModel>();
  final isLoading = false.obs;
  final isCompleting = false.obs;
  final errorMessage = RxnString();

  String get slug => Get.parameters['slug']!;
  int get id => int.parse(Get.parameters['id']!);

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      lesson.value = await _repository.lesson(slug, id);
    } on ApiException catch (error) {
      errorMessage.value = error.message;
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> complete() async {
    isCompleting.value = true;
    try {
      final enrollment = await _repository.complete(slug, id);
      Get.back<void>();
      Get.snackbar(
        enrollment.progress == 100 ? 'Parcours terminé, bravo !' : 'Leçon terminée',
        'Progression : ${enrollment.progress} %',
        snackPosition: SnackPosition.BOTTOM,
      );
    } on ApiException catch (error) {
      Get.snackbar('Oups', error.message, snackPosition: SnackPosition.BOTTOM);
    } finally {
      isCompleting.value = false;
    }
  }
}
