import '../entities/sent_message.dart';
import '../repositories/listening_repository.dart';

final class SendVoiceMessage {
  const SendVoiceMessage(this._repository);

  /// Même limite que l'API.
  static const maxSeconds = 120;

  /// En dessous, l'enregistrement est sans doute un appui involontaire.
  static const minSeconds = 1;

  final ListeningRepository _repository;

  /// Renvoie `null` si l'enregistrement est trop court pour être envoyé.
  Future<SentMessage?> call(int conversationId, String filePath, Duration length) async {
    final seconds = length.inSeconds;
    if (seconds < minSeconds) {
      return null;
    }
    return _repository.sendVoice(conversationId, filePath, seconds.clamp(minSeconds, maxSeconds));
  }
}
