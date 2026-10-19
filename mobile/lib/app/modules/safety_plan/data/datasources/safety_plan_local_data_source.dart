import '../../../../core/storage/secure_cache.dart';
import '../../domain/entities/safety_plan.dart';
import '../models/safety_plan_model.dart';

/// Copie chiffrée sur l'appareil, pour un accès sans réseau.
class SafetyPlanLocalDataSource {
  SafetyPlanLocalDataSource(this._cache);

  static const _key = 'safety_plan';

  final SecureCache _cache;

  Future<SafetyPlan?> read() async {
    final json = await _cache.read(_key);
    return json == null ? null : SafetyPlanModel.fromJson(json);
  }

  Future<void> write(SafetyPlan plan) => _cache.write(_key, SafetyPlanModel.toJson(plan));

  Future<void> clear() => _cache.delete(_key);
}
