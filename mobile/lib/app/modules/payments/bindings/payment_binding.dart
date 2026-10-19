import 'package:get/get.dart';

import '../../../core/services/session_service.dart';
import '../controllers/payment_controller.dart';
import '../repositories/payment_repository.dart';

class PaymentBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() => PaymentController(PaymentRepository(Get.find<SessionService>().api)), fenix: true);
}
