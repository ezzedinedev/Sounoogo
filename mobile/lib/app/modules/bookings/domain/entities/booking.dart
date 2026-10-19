/// Consultation réservée avec un professionnel.
class Booking {
  const Booking({
    required this.id,
    required this.status,
    required this.statusLabel,
    required this.modeLabel,
    required this.price,
    this.startsAt,
    this.professionalName,
  });

  final int id;
  final String status;
  final String statusLabel;
  final String modeLabel;

  /// Prix en francs CFA.
  final int price;
  final DateTime? startsAt;
  final String? professionalName;

  bool get awaitsPayment => status == 'pending_payment';
  bool get isCancellable => status == 'pending_payment' || status == 'confirmed';
}
