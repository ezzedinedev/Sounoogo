import '../../../core/constants/api_constants.dart';
import '../../../core/models/payment_model.dart';
import '../../../core/network/api_provider.dart';

class PaymentRepository {
  PaymentRepository(this._api);

  final ApiProvider _api;

  /// Lance un paiement sur une route « payer » (réservation, formation, abonnement).
  Future<PaymentModel> start(String path, String provider, {Map<String, dynamic> extra = const {}}) async =>
      PaymentModel.fromJson((await _api.post(path, data: {'provider': provider, ...extra}))['data'] as Map<String, dynamic>);

  Future<PaymentModel> find(String reference) async =>
      PaymentModel.fromJson((await _api.get(ApiConstants.payment(reference)))['data'] as Map<String, dynamic>);
}
