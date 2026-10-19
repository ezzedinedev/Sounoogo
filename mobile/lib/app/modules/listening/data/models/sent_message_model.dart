import '../../../../shared/data/models/emergency_contact_model.dart';
import '../../domain/entities/risk_level.dart';
import '../../domain/entities/sent_message.dart';
import 'message_model.dart';

/// Réponse de `POST /conversations/{id}/messages`.
abstract final class SentMessageModel {
  static SentMessage fromJson(Map<String, dynamic> json) => SentMessage(
    message: MessageModel.fromJson(json['message'] as Map<String, dynamic>),
    riskLevel: RiskLevel.fromValue(json['risk_level'] as String?),
    emergencyContacts: (json['emergency_contacts'] as List? ?? const [])
        .map((contact) => EmergencyContactModel.fromJson(contact as Map<String, dynamic>))
        .toList(),
  );
}
