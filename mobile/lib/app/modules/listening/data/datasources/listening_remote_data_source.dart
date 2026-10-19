import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';
import '../../domain/entities/conversation.dart';
import '../../domain/entities/message.dart';
import '../../domain/entities/sent_message.dart';
import '../models/conversation_model.dart';
import '../models/message_model.dart';
import '../models/sent_message_model.dart';

class ListeningRemoteDataSource {
  ListeningRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<List<Conversation>> conversations() async {
    final response = await _api.get(ApiConstants.conversations);
    return (response['data'] as List).map((c) => ConversationModel.fromJson(c as Map<String, dynamic>)).toList();
  }

  Future<Conversation> open(String? topic) async => _conversation(await _api.post(ApiConstants.conversations, data: {'topic': ?topic}));

  Future<Conversation> find(int id) async => _conversation(await _api.get(ApiConstants.conversation(id)));

  /// L'API pagine du plus récent au plus ancien.
  Future<List<Message>> messages(int id) async {
    final response = await _api.get(ApiConstants.conversationMessages(id));
    return (response['data'] as List).map((m) => MessageModel.fromJson(m as Map<String, dynamic>)).toList();
  }

  Future<SentMessage> send(int id, String body) async =>
      SentMessageModel.fromJson(await _api.post(ApiConstants.conversationMessages(id), data: {'body': body}));

  Future<Conversation> close(int id) async => _conversation(await _api.post(ApiConstants.conversationClose(id)));

  Future<void> rate(int id, int rating) => _api.post(ApiConstants.conversationRating(id), data: {'rating': rating});

  Future<void> report(int id, String reason, String? details) =>
      _api.post(ApiConstants.conversationReports(id), data: {'reason': reason, 'details': ?details});

  Conversation _conversation(Map<String, dynamic> response) => ConversationModel.fromJson(response['data'] as Map<String, dynamic>);
}
