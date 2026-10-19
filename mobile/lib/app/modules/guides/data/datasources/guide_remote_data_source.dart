import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';

class GuideRemoteDataSource {
  GuideRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<List<Map<String, dynamic>>> list() async => ((await _api.get(ApiConstants.guides))['data'] as List).cast<Map<String, dynamic>>();

  Future<Map<String, dynamic>> find(String slug) async => (await _api.get(ApiConstants.guide(slug)))['data'] as Map<String, dynamic>;
}
