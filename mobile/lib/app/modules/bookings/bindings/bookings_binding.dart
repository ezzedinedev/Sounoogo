import 'package:get/get.dart';

import '../../../core/services/session_service.dart';
import '../controllers/bookings_controller.dart';
import '../repositories/booking_repository.dart';

class BookingsBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut(() => BookingsController(BookingRepository(Get.find<SessionService>().api)));
}
