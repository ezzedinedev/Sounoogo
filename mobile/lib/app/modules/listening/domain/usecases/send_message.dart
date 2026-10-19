import '../entities/sent_message.dart';
import '../repositories/listening_repository.dart';

final class SendMessage {
  const SendMessage(this._repository);

  final ListeningRepository _repository;

  /// Renvoie `null` si le message est vide après nettoyage : rien n'est envoyé.
  Future<SentMessage?> call(int conversationId, String text) async {
    final body = text.trim();
    return body.isEmpty ? null : _repository.send(conversationId, body);
  }
}
