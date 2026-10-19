import '../../../../core/error/failure.dart';
import '../../../../core/error/failure_mapper.dart';
import '../../domain/entities/safety_plan.dart';
import '../../domain/repositories/safety_plan_repository.dart';
import '../datasources/safety_plan_local_data_source.dart';
import '../datasources/safety_plan_remote_data_source.dart';

class SafetyPlanRepositoryImpl implements SafetyPlanRepository {
  SafetyPlanRepositoryImpl(this._remote, this._local);

  final SafetyPlanRemoteDataSource _remote;
  final SafetyPlanLocalDataSource _local;

  @override
  Future<SafetyPlan?> get() async {
    try {
      final plan = await guardApi(_remote.get);
      plan == null ? await _local.clear() : await _local.write(plan);
      return plan;
    } on NetworkFailure {
      final cached = await _local.read();
      if (cached == null) {
        rethrow;
      }
      return cached;
    }
  }

  @override
  Future<SafetyPlan> save(SafetyPlan plan) async {
    final saved = await guardApi(() => _remote.save(plan));
    await _local.write(saved);
    return saved;
  }

  @override
  Future<void> delete() async {
    await guardApi(_remote.delete);
    await _local.clear();
  }
}
