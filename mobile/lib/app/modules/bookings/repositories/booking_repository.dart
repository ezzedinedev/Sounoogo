import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_provider.dart';
import '../models/booking_model.dart';

class BookingRepository {
  BookingRepository(this._api);

  final ApiProvider _api;

  Future<List<BookingModel>> getAll() async {
    final response = await _api.get(ApiConstants.bookings);
    return (response['data'] as List).map((b) => BookingModel.fromJson(b as Map<String, dynamic>)).toList();
  }

  Future<void> cancel(int id) => _api.post(ApiConstants.bookingCancel(id));
}
