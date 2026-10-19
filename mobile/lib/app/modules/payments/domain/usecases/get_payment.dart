import '../entities/payment.dart';
import '../repositories/payment_repository.dart';

final class GetPayment {
  const GetPayment(this._repository);

  final PaymentRepository _repository;

  Future<Payment> call(String reference) => _repository.find(reference);
}
