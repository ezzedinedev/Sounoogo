import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/error/failure.dart';
import '../../../payments/domain/entities/payment_target.dart';
import '../../../payments/presentation/controllers/payment_controller.dart';
import '../../domain/entities/course.dart';
import '../../domain/entities/enrollment.dart';
import '../../domain/entities/lesson.dart';
import '../../domain/usecases/enroll_in_course.dart';
import '../../domain/usecases/get_course.dart';
import '../../domain/usecases/get_enrollments.dart';
import 'courses_controller.dart';

class CourseDetailController extends GetxController {
  CourseDetailController({required this._getCourse, required this._getEnrollments, required this._enrollInCourse});

  final GetCourse _getCourse;
  final GetEnrollments _getEnrollments;
  final EnrollInCourse _enrollInCourse;
  final PaymentController _payments = Get.find();

  final course = Rxn<Course>();
  final enrollment = Rxn<Enrollment>();
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
      course.value = await _getCourse(slug);
      enrollment.value = await _getEnrollments.forCourse(slug);
    } on Failure catch (failure) {
      errorMessage.value = failure.message;
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> enroll() async {
    isEnrolling.value = true;
    try {
      enrollment.value = await _enrollInCourse(slug);
      Get.find<CoursesController>().load();
    } on PaymentRequiredFailure {
      await _offerPurchase();
    } on Failure catch (failure) {
      Get.snackbar('Inscription impossible', failure.message, snackPosition: SnackPosition.BOTTOM);
    } finally {
      isEnrolling.value = false;
    }
  }

  Future<void> _offerPurchase() async {
    final current = course.value!;
    final choice = await Get.defaultDialog<String>(
      title: 'Formation payante',
      middleText: "Achète-la à l'unité, ou abonne-toi pour accéder à toutes les formations.",
      textConfirm: 'Acheter',
      textCancel: 'Voir les abonnements',
      onConfirm: () => Get.back(result: 'buy'),
      onCancel: () => Get.toNamed(AppRoutes.subscription),
    );
    if (choice == 'buy') {
      await _payments.checkout(CoursePurchase(slug), label: 'Formation « ${current.title} »', amount: current.price);
    }
  }

  void openLesson(Lesson lesson) {
    if (enrollment.value == null && !lesson.isPreview) {
      enroll();
      return;
    }
    Get.toNamed(AppRoutes.lessonOf(slug, lesson.id))?.then((_) => load());
  }
}
