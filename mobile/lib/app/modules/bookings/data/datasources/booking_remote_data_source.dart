import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';
import '../../domain/entities/booking.dart';
import '../models/booking_model.dart';

class BookingRemoteDataSource {
  BookingRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<List<Booking>> getAll() async {
    final response = await _api.get(ApiConstants.bookings);
    return (response['data'] as List).map((b) => BookingModel.fromJson(b as Map<String, dynamic>)).toList();
  }

  Future<Booking> create({required int slotId, required String mode, required bool keepAnonymous, String? note}) async {
    final response = await _api.post(
      ApiConstants.bookings,
      data: {'slot_id': slotId, 'mode': mode, 'keep_anonymous': keepAnonymous, 'note': ?note},
    );
    return BookingModel.fromJson(response['data'] as Map<String, dynamic>);
  }

  Future<void> cancel(int id) => _api.post(ApiConstants.bookingCancel(id));
}
