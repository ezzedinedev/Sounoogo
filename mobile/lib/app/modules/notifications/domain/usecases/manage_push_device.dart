import '../entities/push_event.dart';
import '../repositories/notification_repository.dart';

final class ManagePushDevice {
  const ManagePushDevice(this._repository);

  final NotificationRepository _repository;

  Future<void> start() => _repository.start();

  Future<void> register() => _repository.registerDevice();

  Future<void> unregister() => _repository.unregisterDevice();

  Stream<PushTarget> opened() => _repository.openedTargets();
}
