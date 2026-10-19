import '../entities/booking.dart';
import '../repositories/booking_repository.dart';

final class BookConsultation {
  const BookConsultation(this._repository);

  final BookingRepository _repository;

  /// Par défaut, le professionnel ne voit que le pseudonyme du membre.
  Future<Booking> call({required int slotId, required String mode, bool keepAnonymous = true, String? note}) {
    final cleanNote = note?.trim();
    return _repository.book(
      slotId: slotId,
      mode: mode,
      keepAnonymous: keepAnonymous,
      note: cleanNote == null || cleanNote.isEmpty ? null : cleanNote,
    );
  }
}
