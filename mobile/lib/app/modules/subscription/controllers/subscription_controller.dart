import 'package:get/get.dart';

import '../../../core/constants/api_constants.dart';
import '../../../core/models/plan_model.dart';
import '../../../core/services/session_service.dart';
import '../../payments/controllers/payment_controller.dart';
import '../../payments/screens/payment_provider_sheet.dart';

class SubscriptionController extends GetxController {
  final SessionService _session = Get.find();

  List<PlanModel> get plans => _session.plans;
  SubscriptionModel? get current => _session.user.value?.subscription;

  @override
  void onInit() {
    super.onInit();
    _session.refreshUser().ignore();
  }

  Future<void> subscribe(PlanModel plan) async {
    final provider = await PaymentProviderSheet.show(label: 'Abonnement ${plan.name} · 1 mois', amount: plan.price);
    if (provider != null) {
      await Get.find<PaymentController>().pay(ApiConstants.subscription, provider, extra: {'plan': plan.code});
      await _session.refreshUser();
    }
  }
}
