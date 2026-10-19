import 'package:get/get.dart';

import '../../../core/services/session_service.dart';
import '../controllers/auth_controller.dart';
import '../repositories/auth_repository.dart';

class AuthBinding extends Bindings {
  @override
  void dependencies() {
    if (!Get.isRegistered<AuthController>()) {
      Get.put(AuthController(AuthRepository(Get.find<SessionService>().api)));
    }
  }
}
