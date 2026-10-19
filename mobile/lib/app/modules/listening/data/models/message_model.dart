import '../../domain/entities/message.dart';

abstract final class MessageModel {
  static Message fromJson(Map<String, dynamic> json) {
    final sender = json['sender'] as Map<String, dynamic>?;
    return Message(
      id: json['id'] as int,
      kind: json['kind'] as String? ?? 'text',
      body: json['body'] as String,
      createdAt: DateTime.parse(json['created_at'] as String).toLocal(),
      senderId: sender?['id'] as int?,
      senderName: sender?['display_name'] as String?,
    );
  }
}
