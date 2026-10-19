/// Moyen de paiement proposé par l'API (Orange Money, Wave…).
class PaymentProvider {
  const PaymentProvider({required this.value, required this.label});

  final String value;
  final String label;
}
