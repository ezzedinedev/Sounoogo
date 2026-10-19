/// Moyen de paiement choisi par le membre et, pour le Mobile Money, le numéro à débiter.
/// Le numéro est transmis au prestataire, jamais conservé par SUNOOGO.
class PaymentMethod {
  const PaymentMethod(this.provider, {this.phone});

  final String provider;
  final String? phone;
}
