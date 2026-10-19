import '../repositories/app_lock_repository.dart';

final class GetPinLength {
  const GetPinLength(this._repository);

  final AppLockRepository _repository;

  Future<int?> call() => _repository.pinLength();
}
