import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_provider.dart';
import '../../bookings/models/booking_model.dart';
import '../models/professional_model.dart';

class ProfessionalRepository {
  ProfessionalRepository(this._api);

  final ApiProvider _api;

  Future<List<ProfessionalModel>> getAll({String? specialty, String? mode}) async {
    final response = await _api.get(ApiConstants.professionals, query: {
      'filter[specialty]': ?specialty,
      'filter[mode]': ?mode,
    });
    return (response['data'] as List).map((p) => ProfessionalModel.fromJson(p as Map<String, dynamic>)).toList();
  }

  Future<ProfessionalModel> getById(int id) async =>
      ProfessionalModel.fromJson((await _api.get(ApiConstants.professional(id)))['data'] as Map<String, dynamic>);

  Future<BookingModel> book({required int slotId, required String mode, required bool keepAnonymous, String? note}) async {
    final response = await _api.post(ApiConstants.bookings, data: {
      'slot_id': slotId,
      'mode': mode,
      'keep_anonymous': keepAnonymous,
      'note': ?note,
    });
    return BookingModel.fromJson(response['data'] as Map<String, dynamic>);
  }
}
