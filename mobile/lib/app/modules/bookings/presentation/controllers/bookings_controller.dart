import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../../payments/domain/entities/payment_target.dart';
import '../../../payments/presentation/controllers/payment_controller.dart';
import '../../domain/entities/booking.dart';
import '../../domain/usecases/cancel_booking.dart';
import '../../domain/usecases/get_bookings.dart';

class BookingsController extends GetxController {
  BookingsController({required this._getBookings, required this._cancelBooking});

  final GetBookings _getBookings;
  final CancelBooking _cancelBooking;
  final PaymentController _payments = Get.find();

  final items = <Booking>[].obs;
  final isLoading = false.obs;
  final errorMessage = RxnString();

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      items.assignAll(await _getBookings());
    } on Failure catch (failure) {
      errorMessage.value = failure is NetworkFailure ? 'Impossible de charger tes consultations.' : failure.message;
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> pay(Booking booking) async {
    final paid = await _payments.checkout(
      BookingPayment(booking.id),
      label: 'Consultation avec ${booking.professionalName ?? ''}',
      amount: booking.price,
    );
    if (paid) {
      await load();
    }
  }

  Future<void> cancel(Booking booking) async {
    try {
      await _cancelBooking(booking.id);
      await load();
    } on Failure catch (failure) {
      Get.snackbar('Annulation impossible', failure.message, snackPosition: SnackPosition.BOTTOM);
    }
  }
}
