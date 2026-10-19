import 'dart:async';

import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../routes/app_routes.dart';
import '../../../core/models/payment_model.dart';
import '../../../core/network/api_exception.dart';
import '../repositories/payment_repository.dart';

/// Parcours de paiement : choix du moyen, ouverture de la page Wave / Orange Money,
/// puis suivi de l'état jusqu'à la confirmation du prestataire.
class PaymentController extends GetxController {
  PaymentController(this._repository);

  final PaymentRepository _repository;

  final payment = Rxn<PaymentModel>();
  final isLoading = false.obs;
  final errorMessage = RxnString();
  Timer? _poll;

  Future<void> pay(String path, String provider, {Map<String, dynamic> extra = const {}}) async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      final started = await _repository.start(path, provider, extra: extra);
      payment.value = started;
      final url = started.checkoutUrl;
      if (url != null) {
        await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
      }
      Get.toNamed(AppRoutes.payment(started.reference));
      _watch(started.reference);
    } on ApiException catch (error) {
      errorMessage.value = error.message;
      Get.snackbar('Paiement impossible', error.message, snackPosition: SnackPosition.BOTTOM);
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> reopenCheckout() async {
    final url = payment.value?.checkoutUrl;
    if (url != null) {
      await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
    }
  }

  void _watch(String reference) {
    _poll?.cancel();
    var attempts = 0;
    _poll = Timer.periodic(const Duration(seconds: 4), (timer) async {
      attempts++;
      try {
        final current = await _repository.find(reference);
        payment.value = current;
        if (!current.isPending || attempts > 75) {
          timer.cancel();
        }
      } catch (_) {}
    });
  }

  @override
  void onClose() {
    _poll?.cancel();
    super.onClose();
  }
}
