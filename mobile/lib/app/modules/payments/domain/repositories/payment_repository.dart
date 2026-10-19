import '../entities/payment.dart';
import '../entities/payment_target.dart';

abstract interface class PaymentRepository {
  /// [provider] est la valeur d'un moyen de paiement publié par l'API (`orange_money`, `wave`…).
  Future<Payment> start(PaymentTarget target, String provider);

  Future<Payment> find(String reference);
}
