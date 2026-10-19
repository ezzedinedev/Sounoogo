import '../entities/booking.dart';
import '../repositories/booking_repository.dart';

final class GetBookings {
  const GetBookings(this._repository);

  final BookingRepository _repository;

  Future<List<Booking>> call() => _repository.getAll();
}
