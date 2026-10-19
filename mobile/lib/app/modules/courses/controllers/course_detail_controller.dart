import 'package:get/get.dart';

import '../../../../routes/app_routes.dart';
import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_exception.dart';
import '../../payments/controllers/payment_controller.dart';
import '../../payments/screens/payment_provider_sheet.dart';
import '../models/course_model.dart';
import '../repositories/course_repository.dart';
import 'courses_controller.dart';

class CourseDetailController extends GetxController {
  CourseDetailController(this._repository);

  final CourseRepository _repository;

  final course = Rxn<CourseModel>();
  final enrollment = Rxn<EnrollmentModel>();
  final isLoading = false.obs;
  final isEnrolling = false.obs;
  final errorMessage = RxnString();

  String get slug => Get.parameters['slug']!;

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      course.value = await _repository.getBySlug(slug);
      enrollment.value = (await _repository.enrollments()).where((e) => e.courseSlug == slug).firstOrNull;
    } on ApiException catch (error) {
      errorMessage.value = error.message;
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> enroll() async {
    isEnrolling.value = true;
    try {
      enrollment.value = await _repository.enroll(slug);
      Get.find<CoursesController>().load();
    } on ApiException catch (error) {
      if (error.isPaymentRequired) {
        await _offerPurchase();
      } else {
        Get.snackbar('Inscription impossible', error.message, snackPosition: SnackPosition.BOTTOM);
      }
    } finally {
      isEnrolling.value = false;
    }
  }

  Future<void> _offerPurchase() async {
    final current = course.value!;
    final choice = await Get.defaultDialog<String>(
      title: 'Formation payante',
      middleText: 'Achète-la à l\'unité, ou abonne-toi pour accéder à toutes les formations.',
      textConfirm: 'Acheter',
      textCancel: 'Voir les abonnements',
      onConfirm: () => Get.back(result: 'buy'),
      onCancel: () => Get.toNamed(AppRoutes.subscription),
    );
    if (choice != 'buy') {
      return;
    }
    final provider = await PaymentProviderSheet.show(label: 'Formation « ${current.title} »', amount: current.price);
    if (provider != null) {
      await Get.find<PaymentController>().pay(ApiConstants.coursePurchase(slug), provider);
    }
  }

  void openLesson(LessonModel lesson) {
    final enrolled = enrollment.value != null;
    if (!enrolled && !lesson.isPreview) {
      enroll();
      return;
    }
    Get.toNamed(AppRoutes.lessonOf(slug, lesson.id))?.then((_) => load());
  }
}
