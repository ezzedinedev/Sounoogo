import '../../../../shared/domain/entities/emergency_contact.dart';
import 'message.dart';
import 'risk_level.dart';

/// Message envoyé, avec l'analyse de risque faite par le serveur.
class SentMessage {
  const SentMessage({required this.message, required this.riskLevel, this.emergencyContacts = const []});

  final Message message;
  final RiskLevel riskLevel;

  /// Renseignés quand une détresse est détectée : le superviseur est déjà prévenu côté serveur.
  final List<EmergencyContact> emergencyContacts;

  bool get needsEmergencyHelp => riskLevel == RiskLevel.high;
}
