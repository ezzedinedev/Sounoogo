import 'dart:io';

import 'package:path_provider/path_provider.dart';

import '../../../../core/error/failure_mapper.dart';
import '../../../../shared/domain/entities/user.dart';
import '../../domain/entities/conversation.dart';
import '../../domain/entities/conversation_event.dart';
import '../../domain/entities/message.dart';
import '../../domain/entities/sent_message.dart';
import '../../domain/repositories/listening_repository.dart';
import '../datasources/listening_realtime_data_source.dart';
import '../datasources/listening_remote_data_source.dart';

class ListeningRepositoryImpl implements ListeningRepository {
  ListeningRepositoryImpl(this._remote, this._realtime);

  final ListeningRemoteDataSource _remote;
  final ListeningRealtimeDataSource _realtime;

  @override
  Future<Conversation?> active() => guardApi(() async {
    final conversations = await _remote.conversations();
    return conversations.where((conversation) => !conversation.isClosed).firstOrNull;
  });

  @override
  Future<Conversation> open({String? topic}) => guardApi(() => _remote.open(topic));

  @override
  Future<Conversation> find(int conversationId) => guardApi(() => _remote.find(conversationId));

  @override
  Future<List<Message>> messages(int conversationId) => guardApi(() async => (await _remote.messages(conversationId)).reversed.toList());

  @override
  Future<SentMessage> send(int conversationId, String body) => guardApi(() => _remote.send(conversationId, body));

  @override
  Future<SentMessage> sendVoice(int conversationId, String filePath, int seconds) async {
    try {
      return await guardApi(() => _remote.sendVoice(conversationId, filePath, seconds));
    } finally {
      // L'enregistrement est envoyé ou abandonné : il ne reste pas sur le téléphone.
      await File(filePath).delete().catchError((Object _) => File(filePath));
    }
  }

  @override
  Future<String> voiceFile(Message message) async {
    final directory = Directory('${(await getTemporaryDirectory()).path}/voice');
    await directory.create(recursive: true);
    final file = File('${directory.path}/${message.id}.m4a');
    if (!await file.exists()) {
      await guardApi(() => _remote.download(message.audioUrl!, file.path));
    }
    return file.path;
  }

  @override
  Future<User> preferListenerOf(int conversationId) => guardApi(() => _remote.preferListener(conversationId));

  @override
  Future<User> forgetPreferredListener() => guardApi(_remote.forgetPreferredListener);

  @override
  Future<Conversation> close(int conversationId) => guardApi(() => _remote.close(conversationId));

  @override
  Future<void> rate(int conversationId, int rating) => guardApi(() => _remote.rate(conversationId, rating));

  @override
  Future<void> report(int conversationId, String reason, {String? details}) =>
      guardApi(() => _remote.report(conversationId, reason, details));

  @override
  Stream<ConversationEvent> watch(int conversationId) => _realtime.watch(conversationId);
}
