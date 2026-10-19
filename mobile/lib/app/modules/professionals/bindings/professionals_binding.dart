import 'package:get/get.dart';

import '../../../core/services/session_service.dart';
import '../controllers/professional_detail_controller.dart';
import '../controllers/professionals_controller.dart';
import '../repositories/professional_repository.dart';

class ProfessionalsBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() => ProfessionalsController(ProfessionalRepository(Get.find<SessionService>().api)), fenix: true);
}

class ProfessionalDetailBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() => ProfessionalDetailController(ProfessionalRepository(Get.find<SessionService>().api)));
}
