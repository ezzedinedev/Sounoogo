import '../../domain/entities/payment_provider.dart';
import '../../domain/entities/reference_data.dart';
import 'emergency_contact_model.dart';
import 'plan_model.dart';

/// Réponse de `GET /meta`.
abstract final class ReferenceDataModel {
  static ReferenceData fromJson(Map<String, dynamic> json) => ReferenceData(
    emergencyContacts: _list(json['emergency_contacts'], EmergencyContactModel.fromJson),
    paymentProviders: _list(json['payment_providers'], (p) => PaymentProvider(value: p['value'] as String, label: p['label'] as String)),
    plans: _list(json['plans'], PlanModel.fromJson),
  );

  static List<T> _list<T>(Object? raw, T Function(Map<String, dynamic>) parse) =>
      (raw as List? ?? const []).map((item) => parse(item as Map<String, dynamic>)).toList();
}
