import 'package:get/get.dart';

import '../../../core/services/session_service.dart';
import '../controllers/course_detail_controller.dart';
import '../controllers/courses_controller.dart';
import '../controllers/lesson_controller.dart';
import '../repositories/course_repository.dart';

CourseRepository _repository() => CourseRepository(Get.find<SessionService>().api);

class CoursesBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() => CoursesController(_repository()), fenix: true);
}

class CourseDetailBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() => CourseDetailController(_repository()));
}

class LessonBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() => LessonController(_repository()));
}
