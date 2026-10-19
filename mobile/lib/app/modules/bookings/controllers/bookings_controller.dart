import 'package:get/get.dart';

import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_exception.dart';
import '../../payments/controllers/payment_controller.dart';
import '../../payments/screens/payment_provider_sheet.dart';
import '../models/booking_model.dart';
import '../repositories/booking_repository.dart';

class BookingsController extends GetxController {
  BookingsController(this._repository);

  final BookingRepository _repository;

  final items = <BookingModel>[].obs;
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
      items.assignAll(await _repository.getAll());
    } on ApiException catch (error) {
      errorMessage.value = error.isNetwork ? 'Impossible de charger tes consultations.' : error.message;
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> pay(BookingModel booking) async {
    final provider = await PaymentProviderSheet.show(label: 'Consultation avec ${booking.professionalName ?? ''}', amount: booking.price);
    if (provider != null) {
      await Get.find<PaymentController>().pay(ApiConstants.bookingPay(booking.id), provider);
      await load();
    }
  }

  Future<void> cancel(BookingModel booking) async {
    try {
      await _repository.cancel(booking.id);
      await load();
    } on ApiException catch (error) {
      Get.snackbar('Annulation impossible', error.message, snackPosition: SnackPosition.BOTTOM);
    }
  }
}
