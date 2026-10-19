import '../entities/message.dart';
import '../repositories/listening_repository.dart';

final class GetVoiceFile {
  const GetVoiceFile(this._repository);

  final ListeningRepository _repository;

  Future<String> call(Message message) => _repository.voiceFile(message);
}
