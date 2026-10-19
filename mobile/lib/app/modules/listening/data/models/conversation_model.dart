import '../../domain/entities/conversation.dart';
import '../../domain/entities/risk_level.dart';

abstract final class ConversationModel {
  static Conversation fromJson(Map<String, dynamic> json) => Conversation(
    id: json['id'] as int,
    status: json['status'] as String,
    riskLevel: RiskLevel.fromValue(json['risk_level'] as String?),
    topic: json['topic'] as String?,
    listenerName: (json['listener'] as Map<String, dynamic>?)?['display_name'] as String?,
    rating: json['member_rating'] as int?,
  );
}
