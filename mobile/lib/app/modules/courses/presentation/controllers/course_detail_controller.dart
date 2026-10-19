import 'package:get/get.dart';
import 'package:share_plus/share_plus.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/error/failure.dart';
import '../../../payments/domain/entities/payment_target.dart';
import '../../../payments/presentation/controllers/payment_controller.dart';
import '../../../../core/widgets/app_sheet.dart';
import '../../domain/entities/certificate.dart';
import '../../domain/entities/course.dart';
import '../../domain/entities/enrollment.dart';
import '../../domain/entities/lesson.dart';
import '../../domain/usecases/enroll_in_course.dart';
import '../../domain/usecases/get_course.dart';
import '../../domain/usecases/get_certificate_file.dart';
import '../../domain/usecases/get_enrollments.dart';
import '../../domain/usecases/get_learning_progress.dart';
import '../../domain/usecases/issue_certificate.dart';
import '../widgets/certificate_card.dart';
import 'courses_controller.dart';
import '../../../../core/widgets/app_toast.dart';

class CourseDetailController extends GetxController {
  CourseDetailController({
    required this._getCourse,
    required this._getEnrollments,
    required this._enrollInCourse,
    required this._getLearningProgress,
    required this._issueCertificate,
    required this._getCertificateFile,
  });

  final GetCourse _getCourse;
  final GetEnrollments _getEnrollments;
  final EnrollInCourse _enrollInCourse;
  final GetLearningProgress _getLearningProgress;
  final IssueCertificate _issueCertificate;
  final GetCertificateFile _getCertificateFile;
  final PaymentController _payments = Get.find();

  final course = Rxn<Course>();
  final enrollment = Rxn<Enrollment>();
  final isLoading = false.obs;
  final isEnrolling = false.obs;
  final certificate = Rxn<Certificate>();
  final isCertificateBusy = false.obs;
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
      if (enrollment.value?.isCompleted ?? false) {
        certificate.value = (await _getLearningProgress()).certificateFor(slug);
      }
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
      AppToast.show('Inscription impossible', failure.message);
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

  Future<void> requestCertificate() async {
    final name = await AppSheet.show<String>(const CertificateNameSheet());
    if (name == null) return;

    isCertificateBusy.value = true;
    try {
      certificate.value = await _issueCertificate(slug, name);
      AppToast.show('Attestation prête', 'Tu peux la télécharger et la partager.');
    } on Failure catch (failure) {
      AppToast.show('Attestation impossible', failure.message);
    } finally {
      isCertificateBusy.value = false;
    }
  }

  Future<void> shareCertificate() async {
    final current = certificate.value;
    if (current == null) return;

    isCertificateBusy.value = true;
    try {
      final path = await _getCertificateFile(current);
      await SharePlus.instance.share(
        ShareParams(
          files: [XFile(path, mimeType: 'application/pdf')],
          subject: 'Attestation SUNOOGO — ${current.courseTitle}',
        ),
      );
    } on Failure catch (failure) {
      AppToast.show('Téléchargement impossible', failure.message);
    } finally {
      isCertificateBusy.value = false;
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
