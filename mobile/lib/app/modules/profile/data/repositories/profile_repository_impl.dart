import '../../../../core/error/failure_mapper.dart';
import '../../../../shared/domain/entities/user.dart';
import '../../domain/repositories/profile_repository.dart';
import '../datasources/profile_remote_data_source.dart';

class ProfileRepositoryImpl implements ProfileRepository {
  ProfileRepositoryImpl(this._remote);

  final ProfileRemoteDataSource _remote;

  @override
  Future<User> secureAccount({String? email, String? phone, required String password}) =>
      guardApi(() => _remote.upgrade(email: email, phone: phone, password: password));

  @override
  Future<void> deleteAccount() => guardApi(_remote.delete);
}
