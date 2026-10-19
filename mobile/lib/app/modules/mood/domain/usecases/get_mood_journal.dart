import '../entities/mood_journal.dart';
import '../repositories/mood_repository.dart';

final class GetMoodJournal {
  const GetMoodJournal(this._repository);

  final MoodRepository _repository;

  Future<MoodJournal> call({int days = 30}) => _repository.journal(days: days);
}
