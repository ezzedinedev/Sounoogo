import '../../domain/entities/emergency_contact.dart';

abstract final class EmergencyContactModel {
  static EmergencyContact fromJson(Map<String, dynamic> json) =>
      EmergencyContact(label: json['label'] as String, phone: json['phone'] as String, description: json['description'] as String?);
}
