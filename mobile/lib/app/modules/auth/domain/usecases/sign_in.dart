import '../entities/auth_session.dart';
import '../repositories/auth_repository.dart';

final class SignIn {
  const SignIn(this._repository);

  final AuthRepository _repository;

  Future<AuthSession> call({required String login, required String password}) => _repository.login(login: login.trim(), password: password);
}
