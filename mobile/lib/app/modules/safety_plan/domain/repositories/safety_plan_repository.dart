import '../entities/safety_plan.dart';

abstract interface class SafetyPlanRepository {
  /// Le plan du membre, ou `null` s'il n'en a pas. Hors ligne, renvoie la dernière copie enregistrée
  /// sur l'appareil : le plan doit rester accessible en pleine crise, même sans réseau.
  Future<SafetyPlan?> get();

  Future<SafetyPlan> save(SafetyPlan plan);

  Future<void> delete();
}
