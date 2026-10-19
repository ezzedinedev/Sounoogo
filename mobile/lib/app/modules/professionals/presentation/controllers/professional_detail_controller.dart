import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../../../core/utils/formatters.dart';
import '../../../bookings/domain/usecases/book_consultation.dart';
import '../../../payments/domain/entities/payment_target.dart';
import '../../../payments/presentation/controllers/payment_controller.dart';
import '../../domain/entities/professional.dart';
import '../../domain/entities/slot.dart';
import '../../domain/usecases/get_professional.dart';
import '../../../../core/widgets/app_toast.dart';

class ProfessionalDetailController extends GetxController {
  ProfessionalDetailController({required this._getProfessional, required this._bookConsultation});

  final GetProfessional _getProfessional;
  final BookConsultation _bookConsultation;
  final PaymentController _payments = Get.find();

  final professional = Rxn<Professional>();
  final isLoading = false.obs;
  final isBooking = false.obs;
  final errorMessage = RxnString();

  final selectedSlot = Rxn<Slot>();
  final selectedMode = RxnString();
  final keepAnonymous = true.obs;

  int get id => int.parse(Get.parameters['id']!);

  /// Créneaux groupés par jour pour l'affichage.
  Map<String, List<Slot>> get slotsByDay {
    final grouped = <String, List<Slot>>{};
    for (final slot in professional.value?.slots ?? const <Slot>[]) {
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
      final loaded = await _getProfessional(id);
      professional.value = loaded;
      selectedMode.value = loaded.modes.firstOrNull?.value;
    } on Failure catch (failure) {
      errorMessage.value = failure.message;
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
      final booking = await _bookConsultation(slotId: slot.id, mode: mode, keepAnonymous: keepAnonymous.value);
      final paid = await _payments.checkout(BookingPayment(booking.id), label: 'Consultation avec ${pro.name}', amount: booking.price);
      if (!paid) {
        AppToast.show('Réservation enregistrée', 'Tu pourras la payer depuis « Mes consultations ».');
      }
      selectedSlot.value = null;
      await load();
    } on Failure catch (failure) {
      AppToast.show('Réservation impossible', failure.message);
      await load();
    } finally {
      isBooking.value = false;
    }
  }
}
