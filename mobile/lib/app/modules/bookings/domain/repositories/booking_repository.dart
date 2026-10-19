import '../entities/booking.dart';

abstract interface class BookingRepository {
  Future<List<Booking>> getAll();

  /// Réserve un créneau ; la réservation attend ensuite son paiement.
  Future<Booking> book({required int slotId, required String mode, required bool keepAnonymous, String? note});

  Future<void> cancel(int bookingId);
}
