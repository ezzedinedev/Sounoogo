import '../../../../core/error/failure_mapper.dart';
import '../../domain/entities/auth_session.dart';
import '../../domain/repositories/auth_repository.dart';
import '../datasources/auth_remote_data_source.dart';

class AuthRepositoryImpl implements AuthRepository {
  AuthRepositoryImpl(this._remote);

  final AuthRemoteDataSource _remote;

  @override
  Future<AuthSession> startAnonymous() => guardApi(_remote.anonymous);

  @override
  Future<AuthSession> recover({required String pseudonym, required String recoveryCode}) =>
      guardApi(() => _remote.recover(pseudonym, recoveryCode));

  @override
  Future<AuthSession> login({required String login, required String password}) => guardApi(() => _remote.login(login, password));
}
