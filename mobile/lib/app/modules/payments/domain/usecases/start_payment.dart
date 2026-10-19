import '../entities/payment.dart';
import '../entities/payment_target.dart';
import '../repositories/payment_repository.dart';

final class StartPayment {
  const StartPayment(this._repository);

  final PaymentRepository _repository;

  Future<Payment> call(PaymentTarget target, String provider) => _repository.start(target, provider);
}
