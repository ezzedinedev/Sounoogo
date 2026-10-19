import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:sunoogo/app/core/network/api_provider.dart';
import 'package:sunoogo/app/modules/payments/data/datasources/payment_remote_data_source.dart';
import 'package:sunoogo/app/modules/payments/domain/entities/payment_method.dart';
import 'package:sunoogo/app/modules/payments/domain/entities/payment_target.dart';

class _MockApiProvider extends Mock implements ApiProvider {}

void main() {
  late _MockApiProvider api;
  late PaymentRemoteDataSource source;

  const response = {
    'data': {'reference': 'PAY-1', 'amount': 5000, 'status': 'pending', 'checkout_url': 'https://pay.example/PAY-1'},
  };

  setUp(() {
    api = _MockApiProvider();
    source = PaymentRemoteDataSource(api);
    when(() => api.post(any(), data: any(named: 'data'))).thenAnswer((_) async => response);
  });

  test('chaque cible de paiement appelle sa route', () async {
    const mobileMoney = PaymentMethod('moneyfusion', phone: '70123456');
    await source.start(const BookingPayment(3), mobileMoney);
    await source.start(const CoursePurchase('estime-de-soi'), const PaymentMethod('mock'));
    await source.start(const SubscriptionPayment('essentiel'), mobileMoney);

    verify(() => api.post('/bookings/3/pay', data: {'provider': 'moneyfusion', 'phone': '70123456'})).called(1);
    verify(() => api.post('/courses/estime-de-soi/purchase', data: {'provider': 'mock'})).called(1);
    verify(() => api.post('/subscription', data: {'provider': 'moneyfusion', 'phone': '70123456', 'plan': 'essentiel'})).called(1);
  });

  test('la réponse devient une entité Payment', () async {
    final payment = await source.start(const BookingPayment(3), const PaymentMethod('mock'));

    expect(payment.reference, 'PAY-1');
    expect(payment.isPending, isTrue);
    expect(payment.checkoutUrl, 'https://pay.example/PAY-1');
  });
}
