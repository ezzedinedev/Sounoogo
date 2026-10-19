import '../entities/booking.dart';

abstract interface class BookingRepository {
  Future<List<Booking>> getAll();

  /// Réserve un créneau ; la réservation attend ensuite son paiement.
  Future<Booking> book({required int slotId, required String mode, required bool keepAnonymous, String? note});

  Future<void> cancel(int bookingId);

  /// Lien personnel vers la salle de visio. Échoue (409) si la salle n'est pas encore ouverte.
  Future<Uri> videoLink(int bookingId);
}
