import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';

class EmergencyRemoteDataSource {
  EmergencyRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<void> alert() => _api.post(ApiConstants.emergency);
}
