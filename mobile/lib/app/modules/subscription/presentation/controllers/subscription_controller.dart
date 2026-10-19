import 'package:get/get.dart';

import '../../../../shared/domain/entities/plan.dart';
import '../../../../shared/domain/entities/subscription.dart';
import '../../../../shared/presentation/session_service.dart';
import '../../../payments/domain/entities/payment_target.dart';
import '../../../payments/presentation/controllers/payment_controller.dart';

/// Les offres viennent des données de référence ; le paiement passe par le module paiements.
class SubscriptionController extends GetxController {
  final SessionService _session = Get.find();
  final PaymentController _payments = Get.find();

  List<Plan> get plans => _session.plans;
  Subscription? get current => _session.user.value?.subscription;

  @override
  void onInit() {
    super.onInit();
    _session.refreshUser().ignore();
  }

  Future<void> subscribe(Plan plan) async {
    final paid = await _payments.checkout(SubscriptionPayment(plan.code), label: 'Abonnement ${plan.name} · 1 mois', amount: plan.price);
    if (paid) {
      await _session.refreshUser();
    }
  }
}
