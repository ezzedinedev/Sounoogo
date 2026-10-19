import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';
import '../../domain/entities/payment.dart';
import '../../domain/entities/payment_target.dart';
import '../models/payment_model.dart';

class PaymentRemoteDataSource {
  PaymentRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<Payment> start(PaymentTarget target, String provider) async {
    final (path, extra) = switch (target) {
      BookingPayment(:final bookingId) => (ApiConstants.bookingPay(bookingId), const <String, dynamic>{}),
      CoursePurchase(:final courseSlug) => (ApiConstants.coursePurchase(courseSlug), const <String, dynamic>{}),
      SubscriptionPayment(:final planCode) => (ApiConstants.subscription, <String, dynamic>{'plan': planCode}),
    };
    return _payment(await _api.post(path, data: {'provider': provider, ...extra}));
  }

  Future<Payment> find(String reference) async => _payment(await _api.get(ApiConstants.payment(reference)));

  Payment _payment(Map<String, dynamic> response) => PaymentModel.fromJson(response['data'] as Map<String, dynamic>);
}
