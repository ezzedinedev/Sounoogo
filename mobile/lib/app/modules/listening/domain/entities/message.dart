class Message {
  const Message({
    required this.id,
    required this.kind,
    required this.body,
    required this.createdAt,
    this.senderId,
    this.senderName,
    this.audioUrl,
    this.audioDuration,
  });

  final int id;
  final String kind;
  final String body;
  final DateTime createdAt;
  final int? senderId;
  final String? senderName;

  /// Message vocal : adresse protégée du fichier et durée en secondes.
  final String? audioUrl;
  final int? audioDuration;

  bool get isSystem => kind == 'system';
  bool get isVoice => kind == 'audio' && audioUrl != null;
}
