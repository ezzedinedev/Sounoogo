import '../../../../shared/domain/entities/user.dart';
import '../entities/conversation.dart';
import '../entities/conversation_event.dart';
import '../entities/message.dart';
import '../entities/sent_message.dart';

abstract interface class ListeningRepository {
  /// Conversation non terminée la plus récente, s'il y en a une.
  Future<Conversation?> active();

  Future<Conversation> open({String? topic});

  Future<Conversation> find(int conversationId);

  /// Messages du plus ancien au plus récent.
  Future<List<Message>> messages(int conversationId);

  Future<SentMessage> send(int conversationId, String body);

  /// [filePath] : enregistrement local ; [seconds] : durée.
  Future<SentMessage> sendVoice(int conversationId, String filePath, int seconds);

  /// Fichier local d'un message vocal, téléchargé une fois puis réutilisé.
  Future<String> voiceFile(Message message);

  /// Reparler à l'écoutant de cette conversation la prochaine fois.
  Future<User> preferListenerOf(int conversationId);

  Future<User> forgetPreferredListener();

  Future<Conversation> close(int conversationId);

  Future<void> rate(int conversationId, int rating);

  Future<void> report(int conversationId, String reason, {String? details});

  /// Flux temps réel ; l'annulation de l'abonnement quitte le canal.
  Stream<ConversationEvent> watch(int conversationId);
}
