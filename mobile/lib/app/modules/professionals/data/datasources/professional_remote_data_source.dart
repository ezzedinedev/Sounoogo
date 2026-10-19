import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';
import '../../domain/entities/professional.dart';
import '../models/professional_model.dart';

class ProfessionalRemoteDataSource {
  ProfessionalRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<List<Professional>> getAll({String? specialty, String? mode}) async {
    final response = await _api.get(ApiConstants.professionals, query: {'filter[specialty]': ?specialty, 'filter[mode]': ?mode});
    return (response['data'] as List).map((p) => ProfessionalModel.fromJson(p as Map<String, dynamic>)).toList();
  }

  Future<Professional> getById(int id) async =>
      ProfessionalModel.fromJson((await _api.get(ApiConstants.professional(id)))['data'] as Map<String, dynamic>);
}
