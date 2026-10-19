import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_provider.dart';
import '../models/auth_result_model.dart';

class AuthRepository {
  AuthRepository(this._api);

  final ApiProvider _api;

  Future<AuthResultModel> startAnonymous() async =>
      AuthResultModel.fromJson(await _api.post(ApiConstants.authAnonymous, data: {'device_name': 'mobile', 'locale': 'fr'}));

  Future<AuthResultModel> recover({required String pseudonym, required String code}) async => AuthResultModel.fromJson(
        await _api.post(ApiConstants.authRecover, data: {'pseudonym': pseudonym, 'recovery_code': code, 'device_name': 'mobile'}),
      );

  Future<AuthResultModel> login({required String login, required String password}) async => AuthResultModel.fromJson(
        await _api.post(ApiConstants.authLogin, data: {'login': login, 'password': password, 'device_name': 'mobile'}),
      );
}
