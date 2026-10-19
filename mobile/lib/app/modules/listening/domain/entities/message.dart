class Message {
  const Message({required this.id, required this.kind, required this.body, required this.createdAt, this.senderId, this.senderName});

  final int id;
  final String kind;
  final String body;
  final DateTime createdAt;
  final int? senderId;
  final String? senderName;

  bool get isSystem => kind == 'system';
}
