import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_provider.dart';

class ProfileRepository {
  ProfileRepository(this._api);

  final ApiProvider _api;

  Future<Map<String, dynamic>> upgrade({String? email, String? phone, required String password}) async =>
      (await _api.post(ApiConstants.meUpgrade, data: {
        'email': ?email,
        'phone': ?phone,
        'password': password,
        'password_confirmation': password,
      }))['data'] as Map<String, dynamic>;

  Future<void> deleteAccount() => _api.delete(ApiConstants.me);
}
