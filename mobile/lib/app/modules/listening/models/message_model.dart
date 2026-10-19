class MessageModel {
  const MessageModel({required this.id, required this.kind, required this.body, required this.createdAt, this.senderId, this.senderName});

  factory MessageModel.fromJson(Map<String, dynamic> json) {
    final sender = json['sender'] as Map<String, dynamic>?;
    return MessageModel(
      id: json['id'] as int,
      kind: json['kind'] as String? ?? 'text',
      body: json['body'] as String,
      createdAt: DateTime.parse(json['created_at'] as String).toLocal(),
      senderId: sender?['id'] as int?,
      senderName: sender?['display_name'] as String?,
    );
  }

  final int id;
  final String kind;
  final String body;
  final DateTime createdAt;
  final int? senderId;
  final String? senderName;

  bool get isSystem => kind == 'system';
}
