import 'package:get/get.dart';

import '../../data/datasources/course_remote_data_source.dart';
import '../../data/repositories/course_repository_impl.dart';
import '../../domain/repositories/course_repository.dart';
import '../../domain/usecases/complete_lesson.dart';
import '../../domain/usecases/enroll_in_course.dart';
import '../../domain/usecases/get_course.dart';
import '../../domain/usecases/get_courses.dart';
import '../../domain/usecases/get_certificate_file.dart';
import '../../domain/usecases/get_enrollments.dart';
import '../../domain/usecases/get_learning_progress.dart';
import '../../domain/usecases/issue_certificate.dart';
import '../../domain/usecases/get_lesson.dart';
import '../controllers/course_detail_controller.dart';
import '../controllers/courses_controller.dart';
import '../controllers/lesson_controller.dart';

CourseRepository _repository() {
  Get.lazyPut<CourseRepository>(() => CourseRepositoryImpl(CourseRemoteDataSource(Get.find())), fenix: true);
  return Get.find();
}

class CoursesBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() {
    final repository = _repository();
    return CoursesController(
      getCourses: GetCourses(repository),
      getEnrollments: GetEnrollments(repository),
      getLearningProgress: GetLearningProgress(repository),
    );
  }, fenix: true);
}

class CourseDetailBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() {
    final repository = _repository();
    return CourseDetailController(
      getCourse: GetCourse(repository),
      getEnrollments: GetEnrollments(repository),
      enrollInCourse: EnrollInCourse(repository),
      getLearningProgress: GetLearningProgress(repository),
      issueCertificate: IssueCertificate(repository),
      getCertificateFile: GetCertificateFile(repository),
    );
  });
}

class LessonBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() {
    final repository = _repository();
    return LessonController(getLesson: GetLesson(repository), completeLesson: CompleteLesson(repository));
  });
}
