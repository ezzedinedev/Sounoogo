import '../repositories/listening_repository.dart';

/// Signale un comportement inapproprié à l'équipe de modération.
final class ReportConversation {
  const ReportConversation(this._repository);

  final ListeningRepository _repository;

  Future<void> call(int conversationId, String reason, {String? details}) => _repository.report(conversationId, reason, details: details);
}
