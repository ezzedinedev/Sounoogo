import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';
import '../../../../shared/data/models/user_model.dart';
import '../../../../shared/domain/entities/user.dart';

class ProfileRemoteDataSource {
  ProfileRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<User> upgrade({String? email, String? phone, required String password}) async {
    final response = await _api.post(
      ApiConstants.meUpgrade,
      data: {'email': ?email, 'phone': ?phone, 'password': password, 'password_confirmation': password},
    );
    return UserModel.fromJson(response['data'] as Map<String, dynamic>);
  }

  Future<void> delete() => _api.delete(ApiConstants.me);
}
