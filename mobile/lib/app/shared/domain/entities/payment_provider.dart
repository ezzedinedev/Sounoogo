/// Moyen de paiement proposé par l'API (Mobile Money via MoneyFusion, test…).
class PaymentProvider {
  const PaymentProvider({required this.value, required this.label, this.requiresPhone = false});

  final String value;
  final String label;

  /// Le prestataire débite un compte Mobile Money : il faut demander le numéro.
  final bool requiresPhone;
}
