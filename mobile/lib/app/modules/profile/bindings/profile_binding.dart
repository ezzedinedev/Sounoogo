import 'package:get/get.dart';

import '../../../core/services/session_service.dart';
import '../controllers/profile_controller.dart';
import '../repositories/profile_repository.dart';

class ProfileBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() => ProfileController(ProfileRepository(Get.find<SessionService>().api)), fenix: true);
}
