import '../entities/feeling.dart';
import '../entities/mood_journal.dart';
import '../entities/mood_level.dart';
import '../repositories/mood_repository.dart';

/// Check-in du jour : un nouveau check-in le même jour remplace le précédent.
final class RecordMood {
  const RecordMood(this._repository);

  final MoodRepository _repository;

  Future<MoodJournal> call(MoodLevel level, {List<String> feelings = const [], String? note}) {
    final cleanNote = note?.trim();
    return _repository.record(
      level,
      feelings: feelings.toSet().take(Feeling.maxPerCheckIn).toList(),
      note: cleanNote == null || cleanNote.isEmpty ? null : cleanNote,
    );
  }
}
