import '../../../core/constants/api_constants.dart';
import '../../../core/models/emergency_contact_model.dart';
import '../../../core/network/api_provider.dart';
import '../models/conversation_model.dart';
import '../models/message_model.dart';

class SentMessage {
  const SentMessage(this.message, this.riskLevel, this.emergencyContacts);

  final MessageModel message;
  final String riskLevel;
  final List<EmergencyContactModel> emergencyContacts;
}

class ListeningRepository {
  ListeningRepository(this._api);

  final ApiProvider _api;

  /// Conversation ouverte la plus récente, s'il y en a une.
  Future<ConversationModel?> current() async {
    final response = await _api.get(ApiConstants.conversations);
    final open = (response['data'] as List).cast<Map<String, dynamic>>().where((c) => c['status'] != 'closed');
    return open.isEmpty ? null : ConversationModel.fromJson(open.first);
  }

  Future<ConversationModel> open({String? topic}) async =>
      ConversationModel.fromJson((await _api.post(ApiConstants.conversations, data: {'topic': ?topic}))['data'] as Map<String, dynamic>);

  Future<ConversationModel> find(int id) async =>
      ConversationModel.fromJson((await _api.get(ApiConstants.conversation(id)))['data'] as Map<String, dynamic>);

  Future<List<MessageModel>> messages(int conversationId) async {
    final response = await _api.get(ApiConstants.conversationMessages(conversationId));
    return (response['data'] as List).map((m) => MessageModel.fromJson(m as Map<String, dynamic>)).toList().reversed.toList();
  }

  Future<SentMessage> send(int conversationId, String body) async {
    final response = await _api.post(ApiConstants.conversationMessages(conversationId), data: {'body': body});
    return SentMessage(
      MessageModel.fromJson(response['message'] as Map<String, dynamic>),
      response['risk_level'] as String? ?? 'none',
      (response['emergency_contacts'] as List? ?? const []).map((c) => EmergencyContactModel.fromJson(c as Map<String, dynamic>)).toList(),
    );
  }

  Future<ConversationModel> close(int conversationId) async =>
      ConversationModel.fromJson((await _api.post(ApiConstants.conversationClose(conversationId)))['data'] as Map<String, dynamic>);

  Future<void> rate(int conversationId, int rating) => _api.post(ApiConstants.conversationRating(conversationId), data: {'rating': rating});

  Future<void> report(int conversationId, String reason, {String? details}) =>
      _api.post(ApiConstants.conversationReports(conversationId), data: {'reason': reason, 'details': ?details});
}
