import '../repositories/safety_plan_repository.dart';

final class DeleteSafetyPlan {
  const DeleteSafetyPlan(this._repository);

  final SafetyPlanRepository _repository;

  Future<void> call() => _repository.delete();
}
