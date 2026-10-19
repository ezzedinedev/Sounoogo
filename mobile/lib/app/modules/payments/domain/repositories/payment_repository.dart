import '../entities/payment.dart';
import '../entities/payment_method.dart';
import '../entities/payment_target.dart';

abstract interface class PaymentRepository {
  Future<Payment> start(PaymentTarget target, PaymentMethod method);

  Future<Payment> find(String reference);
}
