import '../../domain/entities/payment.dart';

abstract final class PaymentModel {
  static Payment fromJson(Map<String, dynamic> json) => Payment(
    reference: json['reference'] as String,
    amount: json['amount'] as int,
    status: json['status'] as String,
    label: json['label'] as String?,
    checkoutUrl: json['checkout_url'] as String?,
  );
}
