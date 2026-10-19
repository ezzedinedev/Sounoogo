import '../repositories/booking_repository.dart';

final class CancelBooking {
  const CancelBooking(this._repository);

  final BookingRepository _repository;

  Future<void> call(int bookingId) => _repository.cancel(bookingId);
}
