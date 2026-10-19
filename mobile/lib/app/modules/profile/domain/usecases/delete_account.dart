import '../repositories/profile_repository.dart';

final class DeleteAccount {
  const DeleteAccount(this._repository);

  final ProfileRepository _repository;

  Future<void> call() => _repository.deleteAccount();
}
