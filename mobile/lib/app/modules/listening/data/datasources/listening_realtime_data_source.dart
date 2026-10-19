import 'dart:async';

import '../../../../core/error/failure.dart';
import '../../../../core/realtime/realtime_service.dart';
import '../../domain/entities/conversation_event.dart';
import '../models/conversation_model.dart';
import '../models/message_model.dart';

/// Événements Reverb du canal privé `conversation.{id}`.
class ListeningRealtimeDataSource {
  ListeningRealtimeDataSource(this._realtime);

  final RealtimeService _realtime;

  Stream<ConversationEvent> watch(int conversationId) {
    final channel = 'conversation.$conversationId';
    final subscriptions = <StreamSubscription<Map<String, dynamic>>>[];
    late final StreamController<ConversationEvent> events;

    Future<void> bind(String event, ConversationEvent Function(Map<String, dynamic>) parse) async {
      final stream = await _realtime.listen(channel, event);
      subscriptions.add(stream.listen((data) => events.add(parse(data))));
    }

    events = StreamController<ConversationEvent>(
      onListen: () async {
        try {
          await bind('message.sent', (data) => MessageReceived(MessageModel.fromJson(data['message'] as Map<String, dynamic>)));
          await bind(
            'conversation.updated',
            (data) => ConversationUpdated(ConversationModel.fromJson(data['conversation'] as Map<String, dynamic>)),
          );
        } catch (_) {
          events.addError(const NetworkFailure('Temps réel indisponible.'));
        }
      },
      onCancel: () async {
        for (final subscription in subscriptions) {
          await subscription.cancel();
        }
        _realtime.leave(channel);
      },
    );

    return events.stream;
  }
}
