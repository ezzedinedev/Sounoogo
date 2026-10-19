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
    this.video,
  });

  final int id;
  final String status;
  final String statusLabel;
  final String modeLabel;

  /// Prix en francs CFA.
  final int price;
  final DateTime? startsAt;
  final String? professionalName;

  /// Salle de téléconsultation, si la consultation a lieu en visio sur SUNOOGO.
  final VideoRoom? video;

  bool get awaitsPayment => status == 'pending_payment';
  bool get isCancellable => status == 'pending_payment' || status == 'confirmed';
}

/// Fenêtre d'ouverture de la salle de visio (quelques minutes avant le rendez-vous jusqu'après sa fin).
class VideoRoom {
  const VideoRoom({required this.opensAt, required this.closesAt});

  final DateTime opensAt;
  final DateTime closesAt;

  bool isOpenAt(DateTime now) => !now.isBefore(opensAt) && !now.isAfter(closesAt);

  bool isOverAt(DateTime now) => now.isAfter(closesAt);
}
