import '../entities/booking.dart';
import '../repositories/booking_repository.dart';

/// Lien vers la salle de visio d'une consultation, seulement pendant sa fenêtre d'ouverture.
final class JoinVideoConsultation {
  const JoinVideoConsultation(this._repository, {DateTime Function()? clock}) : _clock = clock ?? DateTime.now;

  final BookingRepository _repository;
  final DateTime Function() _clock;

  /// Null si la consultation n'a pas lieu en visio ou si la salle est fermée.
  Future<Uri?> call(Booking booking) async {
    final room = booking.video;
    if (room == null || !room.isOpenAt(_clock())) return null;
    return _repository.videoLink(booking.id);
  }
}
