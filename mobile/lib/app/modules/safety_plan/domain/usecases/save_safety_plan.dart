import '../entities/safety_plan.dart';
import '../repositories/safety_plan_repository.dart';

/// Enregistre le plan après avoir retiré les lignes et contacts laissés vides.
final class SaveSafetyPlan {
  const SaveSafetyPlan(this._repository);

  final SafetyPlanRepository _repository;

  Future<SafetyPlan> call(SafetyPlan plan) => _repository.save(
    SafetyPlan(
      warningSigns: _clean(plan.warningSigns),
      copingStrategies: _clean(plan.copingStrategies),
      distractions: _clean(plan.distractions),
      supportContacts: _contacts(plan.supportContacts),
      professionalContacts: _contacts(plan.professionalContacts),
      safeEnvironment: _clean(plan.safeEnvironment),
      reasonsToLive: _clean(plan.reasonsToLive),
    ),
  );

  static List<String> _clean(List<String> items) => items.map((item) => item.trim()).where((item) => item.isNotEmpty).toList();

  static List<SafetyContact> _contacts(List<SafetyContact> contacts) => contacts
      .map((c) => SafetyContact(name: c.name.trim(), phone: c.phone.replaceAll(RegExp(r'[\s.-]'), '')))
      .where((c) => c.name.isNotEmpty && c.phone.isNotEmpty)
      .toList();
}
