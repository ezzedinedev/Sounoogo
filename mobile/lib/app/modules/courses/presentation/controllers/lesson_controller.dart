import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../domain/entities/lesson.dart';
import '../../domain/usecases/complete_lesson.dart';
import '../../domain/usecases/get_lesson.dart';

class LessonController extends GetxController {
  LessonController({required this._getLesson, required this._completeLesson});

  final GetLesson _getLesson;
  final CompleteLesson _completeLesson;

  final lesson = Rxn<Lesson>();
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
      lesson.value = await _getLesson(slug, id);
    } on Failure catch (failure) {
      errorMessage.value = failure.message;
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> complete() async {
    isCompleting.value = true;
    try {
      final enrollment = await _completeLesson(slug, id);
      Get.back<void>();
      Get.snackbar(
        enrollment.isCompleted ? 'Parcours terminé, bravo !' : 'Leçon terminée',
        'Progression : ${enrollment.progress} %',
        snackPosition: SnackPosition.BOTTOM,
      );
    } on Failure catch (failure) {
      Get.snackbar('Oups', failure.message, snackPosition: SnackPosition.BOTTOM);
    } finally {
      isCompleting.value = false;
    }
  }
}
