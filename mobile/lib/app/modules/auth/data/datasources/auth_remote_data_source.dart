import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';
import '../../domain/entities/auth_session.dart';
import '../models/auth_session_model.dart';

class AuthRemoteDataSource {
  AuthRemoteDataSource(this._api);

  static const _device = 'mobile';

  final ApiProvider _api;

  Future<AuthSession> anonymous() async =>
      AuthSessionModel.fromJson(await _api.post(ApiConstants.authAnonymous, data: {'device_name': _device, 'locale': 'fr'}));

  Future<AuthSession> recover(String pseudonym, String recoveryCode) async => AuthSessionModel.fromJson(
    await _api.post(ApiConstants.authRecover, data: {'pseudonym': pseudonym, 'recovery_code': recoveryCode, 'device_name': _device}),
  );

  Future<AuthSession> login(String login, String password) async => AuthSessionModel.fromJson(
    await _api.post(ApiConstants.authLogin, data: {'login': login, 'password': password, 'device_name': _device}),
  );
}
