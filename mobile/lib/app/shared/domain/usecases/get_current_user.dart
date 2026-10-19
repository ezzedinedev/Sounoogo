import '../entities/user.dart';
import '../repositories/session_repository.dart';

final class GetCurrentUser {
  const GetCurrentUser(this._repository);

  final SessionRepository _repository;

  Future<User> call() => _repository.currentUser();
}
