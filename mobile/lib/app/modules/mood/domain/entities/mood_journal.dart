import 'mood_entry.dart';
import 'mood_summary.dart';

class MoodJournal {
  const MoodJournal({required this.entries, required this.summary});

  /// Du plus ancien au plus récent.
  final List<MoodEntry> entries;
  final MoodSummary summary;

  MoodEntry? get today {
    final now = DateTime.now();
    return entries.where((e) => e.date.year == now.year && e.date.month == now.month && e.date.day == now.day).firstOrNull;
  }
}
