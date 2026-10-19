import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_provider.dart';
import '../../domain/entities/reference_data.dart';
import '../models/reference_data_model.dart';

class ReferenceRemoteDataSource {
  ReferenceRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<ReferenceData> fetch() async => ReferenceDataModel.fromJson(await _api.get(ApiConstants.meta));
}
