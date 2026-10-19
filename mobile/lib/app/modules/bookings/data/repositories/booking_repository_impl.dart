import '../../../../core/error/failure_mapper.dart';
import '../../domain/entities/booking.dart';
import '../../domain/repositories/booking_repository.dart';
import '../datasources/booking_remote_data_source.dart';

class BookingRepositoryImpl implements BookingRepository {
  BookingRepositoryImpl(this._remote);

  final BookingRemoteDataSource _remote;

  @override
  Future<List<Booking>> getAll() => guardApi(_remote.getAll);

  @override
  Future<Booking> book({required int slotId, required String mode, required bool keepAnonymous, String? note}) =>
      guardApi(() => _remote.create(slotId: slotId, mode: mode, keepAnonymous: keepAnonymous, note: note));

  @override
  Future<void> cancel(int bookingId) => guardApi(() => _remote.cancel(bookingId));

  @override
  Future<Uri> videoLink(int bookingId) => guardApi(() => _remote.videoLink(bookingId));
}
