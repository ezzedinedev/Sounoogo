import 'package:get/get.dart';

import '../../../core/services/session_service.dart';
import '../controllers/listening_controller.dart';
import '../repositories/listening_repository.dart';

class ListeningBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() => ListeningController(ListeningRepository(Get.find<SessionService>().api)), fenix: true);
}
