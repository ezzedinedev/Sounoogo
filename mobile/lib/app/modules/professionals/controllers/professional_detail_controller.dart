import 'package:get/get.dart';

import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_exception.dart';
import '../../../core/utils/formatters.dart';
import '../../payments/controllers/payment_controller.dart';
import '../../payments/screens/payment_provider_sheet.dart';
import '../models/professional_model.dart';
import '../repositories/professional_repository.dart';

class ProfessionalDetailController extends GetxController {
  ProfessionalDetailController(this._repository);

  final ProfessionalRepository _repository;

  final professional = Rxn<ProfessionalModel>();
  final isLoading = false.obs;
  final isBooking = false.obs;
  final errorMessage = RxnString();

  final selectedSlot = Rxn<SlotModel>();
  final selectedMode = RxnString();
  final keepAnonymous = true.obs;

  int get id => int.parse(Get.parameters['id']!);

  /// Créneaux groupés par jour pour l'affichage.
  Map<String, List<SlotModel>> get slotsByDay {
    final grouped = <String, List<SlotModel>>{};
    for (final slot in professional.value?.slots ?? const <SlotModel>[]) {
      grouped.putIfAbsent(Formatters.day(slot.startsAt), () => []).add(slot);
    }
    return grouped;
  }

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      final loaded = await _repository.getById(id);
      professional.value = loaded;
      selectedMode.value = loaded.modes.firstOrNull?.value;
    } on ApiException catch (error) {
      errorMessage.value = error.message;
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> bookAndPay() async {
    final slot = selectedSlot.value;
    final mode = selectedMode.value;
    final pro = professional.value;
    if (slot == null || mode == null || pro == null || isBooking.value) {
      return;
    }

    isBooking.value = true;
    try {
      final booking = await _repository.book(slotId: slot.id, mode: mode, keepAnonymous: keepAnonymous.value);
      final provider = await PaymentProviderSheet.show(label: 'Consultation avec ${pro.name}', amount: booking.price);
      if (provider != null) {
        await Get.find<PaymentController>().pay(ApiConstants.bookingPay(booking.id), provider);
      } else {
        Get.snackbar('Réservation enregistrée', 'Tu pourras la payer depuis « Mes consultations ».', snackPosition: SnackPosition.BOTTOM);
      }
      await load();
      selectedSlot.value = null;
    } on ApiException catch (error) {
      Get.snackbar('Réservation impossible', error.message, snackPosition: SnackPosition.BOTTOM);
      await load();
    } finally {
      isBooking.value = false;
    }
  }
}
