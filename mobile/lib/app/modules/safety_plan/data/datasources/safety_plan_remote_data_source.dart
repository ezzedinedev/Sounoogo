import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';
import '../../domain/entities/safety_plan.dart';
import '../models/safety_plan_model.dart';

class SafetyPlanRemoteDataSource {
  SafetyPlanRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<SafetyPlan?> get() async {
    final data = (await _api.get(ApiConstants.safetyPlan))['data'] as Map<String, dynamic>?;
    return data == null ? null : SafetyPlanModel.fromJson(data);
  }

  Future<SafetyPlan> save(SafetyPlan plan) async {
    final payload = SafetyPlanModel.toJson(plan)..remove('updated_at');
    return SafetyPlanModel.fromJson((await _api.put(ApiConstants.safetyPlan, data: payload))['data'] as Map<String, dynamic>);
  }

  Future<void> delete() => _api.delete(ApiConstants.safetyPlan);
}
