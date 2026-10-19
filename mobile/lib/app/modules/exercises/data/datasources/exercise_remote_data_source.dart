import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';

class ExerciseRemoteDataSource {
  ExerciseRemoteDataSource(this._api);

  final ApiProvider _api;

  /// JSON brut : il est aussi conservé tel quel pour l'usage hors ligne.
  Future<List<Map<String, dynamic>>> fetch() async =>
      ((await _api.get(ApiConstants.exercises))['data'] as List).cast<Map<String, dynamic>>();
}
