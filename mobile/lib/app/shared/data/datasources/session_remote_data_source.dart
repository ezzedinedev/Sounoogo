import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_provider.dart';
import '../../domain/entities/user.dart';
import '../models/user_model.dart';

class SessionRemoteDataSource {
  SessionRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<User> me() async => UserModel.fromJson((await _api.get(ApiConstants.me))['data'] as Map<String, dynamic>);

  Future<void> logout() => _api.post(ApiConstants.authLogout);
}
