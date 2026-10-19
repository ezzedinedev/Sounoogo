import '../../domain/entities/safety_plan.dart';

/// Même forme JSON pour l'API (`/safety-plan`) et la copie locale chiffrée.
abstract final class SafetyPlanModel {
  static SafetyPlan fromJson(Map<String, dynamic> json) => SafetyPlan(
    warningSigns: _texts(json['warning_signs']),
    copingStrategies: _texts(json['coping_strategies']),
    distractions: _texts(json['distractions']),
    supportContacts: _contacts(json['support_contacts']),
    professionalContacts: _contacts(json['professional_contacts']),
    safeEnvironment: _texts(json['safe_environment']),
    reasonsToLive: _texts(json['reasons_to_live']),
    updatedAt: DateTime.tryParse(json['updated_at'] as String? ?? '')?.toLocal(),
  );

  static Map<String, dynamic> toJson(SafetyPlan plan) => {
    'warning_signs': plan.warningSigns,
    'coping_strategies': plan.copingStrategies,
    'distractions': plan.distractions,
    'support_contacts': [for (final c in plan.supportContacts) _contactJson(c)],
    'professional_contacts': [for (final c in plan.professionalContacts) _contactJson(c)],
    'safe_environment': plan.safeEnvironment,
    'reasons_to_live': plan.reasonsToLive,
    if (plan.updatedAt != null) 'updated_at': plan.updatedAt!.toUtc().toIso8601String(),
  };

  static Map<String, String> _contactJson(SafetyContact contact) => {'name': contact.name, 'phone': contact.phone};

  static List<String> _texts(Object? raw) => (raw as List? ?? const []).cast<String>();

  static List<SafetyContact> _contacts(Object? raw) => (raw as List? ?? const [])
      .map((c) => SafetyContact(name: (c as Map<String, dynamic>)['name'] as String, phone: c['phone'] as String))
      .toList();
}
