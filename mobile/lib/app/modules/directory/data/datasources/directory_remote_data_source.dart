import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';

class DirectoryRemoteDataSource {
  DirectoryRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<Map<String, dynamic>> search({String? city, String? category}) =>
      _api.get(ApiConstants.resources, query: {'city': ?city, 'category': ?category});
}
