import 'dart:async';

import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/error/failure.dart';
import '../../domain/entities/payment.dart';
import '../../domain/entities/payment_target.dart';
import '../../domain/usecases/get_payment.dart';
import '../../domain/usecases/start_payment.dart';
import '../widgets/payment_provider_sheet.dart';

/// Parcours de paiement : choix du moyen, ouverture de la page Wave / Orange Money,
/// puis suivi de l'état jusqu'à la confirmation du prestataire.
class PaymentController extends GetxController {
  PaymentController({required this._startPayment, required this._getPayment});

  final StartPayment _startPayment;
  final GetPayment _getPayment;

  static const _pollInterval = Duration(seconds: 4);
  static const _maxPolls = 75;

  final payment = Rxn<Payment>();
  final isLoading = false.obs;
  final errorMessage = RxnString();
  Timer? _poll;

  /// Demande le moyen de paiement puis lance le paiement.
  /// Renvoie `false` si le membre a fermé le choix sans payer.
  Future<bool> checkout(PaymentTarget target, {required String label, required int amount}) async {
    final provider = await PaymentProviderSheet.show(label: label, amount: amount);
    if (provider == null) {
      return false;
    }
    await pay(target, provider);
    return true;
  }

  Future<void> pay(PaymentTarget target, String provider) async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      final started = await _startPayment(target, provider);
      payment.value = started;
      await _openCheckout(started);
      Get.toNamed(AppRoutes.payment(started.reference));
      _watch(started.reference);
    } on Failure catch (failure) {
      errorMessage.value = failure.message;
      Get.snackbar('Paiement impossible', failure.message, snackPosition: SnackPosition.BOTTOM);
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> reopenCheckout() async {
    final current = payment.value;
    if (current != null) {
      await _openCheckout(current);
    }
  }

  Future<void> _openCheckout(Payment target) async {
    final url = target.checkoutUrl;
    if (url != null) {
      await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
    }
  }

  void _watch(String reference) {
    _poll?.cancel();
    var attempts = 0;
    _poll = Timer.periodic(_pollInterval, (timer) async {
      attempts++;
      try {
        final current = await _getPayment(reference);
        payment.value = current;
        if (!current.isPending) {
          timer.cancel();
        }
      } on Failure {
        // Nouvel essai au prochain passage.
      }
      if (attempts >= _maxPolls) {
        timer.cancel();
      }
    });
  }

  @override
  void onClose() {
    _poll?.cancel();
    super.onClose();
  }
}
