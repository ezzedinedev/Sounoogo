class ConversationModel {
  const ConversationModel({
    required this.id,
    required this.status,
    required this.riskLevel,
    this.topic,
    this.listenerName,
    this.rating,
  });

  factory ConversationModel.fromJson(Map<String, dynamic> json) => ConversationModel(
        id: json['id'] as int,
        status: json['status'] as String,
        riskLevel: json['risk_level'] as String? ?? 'none',
        topic: json['topic'] as String?,
        listenerName: (json['listener'] as Map<String, dynamic>?)?['display_name'] as String?,
        rating: json['member_rating'] as int?,
      );

  final int id;
  final String status;
  final String riskLevel;
  final String? topic;
  final String? listenerName;
  final int? rating;

  bool get isWaiting => status == 'waiting';
  bool get isClosed => status == 'closed';
}
