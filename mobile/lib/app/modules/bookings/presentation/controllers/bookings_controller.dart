import 'dart:async';

import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../core/error/failure.dart';
import '../../../payments/domain/entities/payment_target.dart';
import '../../../payments/presentation/controllers/payment_controller.dart';
import '../../domain/entities/booking.dart';
import '../../domain/usecases/cancel_booking.dart';
import '../../domain/usecases/get_bookings.dart';
import '../../domain/usecases/join_video_consultation.dart';
import '../../../../core/widgets/app_toast.dart';

class BookingsController extends GetxController {
  BookingsController({required this._getBookings, required this._cancelBooking, required this._joinVideoConsultation});

  final GetBookings _getBookings;
  final CancelBooking _cancelBooking;
  final JoinVideoConsultation _joinVideoConsultation;
  final PaymentController _payments = Get.find();

  final items = <Booking>[].obs;
  final isLoading = false.obs;
  final errorMessage = RxnString();
  final isJoining = RxnInt();

  /// Heure courante, rafraîchie chaque minute : le bouton de visio apparaît à l'ouverture de la salle.
  final now = DateTime.now().obs;
  Timer? _clock;

  @override
  void onInit() {
    super.onInit();
    load();
    _clock = Timer.periodic(const Duration(seconds: 30), (_) => now.value = DateTime.now());
  }

  @override
  void onClose() {
    _clock?.cancel();
    super.onClose();
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

  Future<void> joinVideo(Booking booking) async {
    isJoining.value = booking.id;
    try {
      final link = await _joinVideoConsultation(booking);
      if (link == null) {
        AppToast.show('Salle fermée', 'La salle de visio s\'ouvre dix minutes avant le rendez-vous.');
        return;
      }
      // Ouverture hors de l'app : application Jitsi Meet si elle est installée, sinon le navigateur.
      if (!await launchUrl(link, mode: LaunchMode.externalApplication)) {
        AppToast.show('Visio indisponible', 'Impossible d\'ouvrir la salle sur ce téléphone.');
      }
    } on Failure catch (failure) {
      AppToast.show('Visio indisponible', failure.message);
    } finally {
      isJoining.value = null;
    }
  }

  Future<void> cancel(Booking booking) async {
    try {
      await _cancelBooking(booking.id);
      await load();
    } on Failure catch (failure) {
      AppToast.show('Annulation impossible', failure.message);
    }
  }
}
