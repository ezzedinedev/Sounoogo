import 'risk_level.dart';

class Conversation {
  const Conversation({required this.id, required this.status, required this.riskLevel, this.topic, this.listenerName, this.rating});

  final int id;
  final String status;
  final RiskLevel riskLevel;
  final String? topic;
  final String? listenerName;
  final int? rating;

  bool get isWaiting => status == 'waiting';
  bool get isClosed => status == 'closed';
}
