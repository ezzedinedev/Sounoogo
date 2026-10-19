import '../entities/safety_plan.dart';
import '../repositories/safety_plan_repository.dart';

final class GetSafetyPlan {
  const GetSafetyPlan(this._repository);

  final SafetyPlanRepository _repository;

  Future<SafetyPlan?> call() => _repository.get();
}
