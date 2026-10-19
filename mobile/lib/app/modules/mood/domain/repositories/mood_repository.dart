import '../entities/mood_journal.dart';
import '../entities/mood_level.dart';

abstract interface class MoodRepository {
  Future<MoodJournal> journal({int days = 30});

  /// Enregistre le check-in du jour et renvoie le journal à jour.
  Future<MoodJournal> record(MoodLevel level, {List<String> feelings = const [], String? note});
}
