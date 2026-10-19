class PaymentProviderModel {
  const PaymentProviderModel({required this.value, required this.label});

  factory PaymentProviderModel.fromJson(Map<String, dynamic> json) =>
      PaymentProviderModel(value: json['value'] as String, label: json['label'] as String);

  final String value;
  final String label;
}

class PaymentModel {
  const PaymentModel({required this.reference, required this.amount, required this.status, this.label, this.checkoutUrl});

  factory PaymentModel.fromJson(Map<String, dynamic> json) => PaymentModel(
        reference: json['reference'] as String,
        amount: json['amount'] as int,
        status: json['status'] as String,
        label: json['label'] as String?,
        checkoutUrl: json['checkout_url'] as String?,
      );

  final String reference;
  final int amount;
  final String status;
  final String? label;
  final String? checkoutUrl;

  bool get isPending => status == 'pending';
  bool get isSucceeded => status == 'succeeded';
}
