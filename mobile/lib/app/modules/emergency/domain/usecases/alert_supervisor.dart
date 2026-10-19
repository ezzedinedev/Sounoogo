import '../repositories/emergency_repository.dart';

final class AlertSupervisor {
  const AlertSupervisor(this._repository);

  final EmergencyRepository _repository;

  Future<void> call() => _repository.alertSupervisor();
}
