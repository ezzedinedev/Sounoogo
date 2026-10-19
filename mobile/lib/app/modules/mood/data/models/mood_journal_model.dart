import '../../domain/entities/mood_entry.dart';
import '../../domain/entities/mood_journal.dart';
import '../../domain/entities/mood_level.dart';
import '../../domain/entities/mood_summary.dart';

abstract final class MoodEntryModel {
  static MoodEntry fromJson(Map<String, dynamic> json) => MoodEntry(
    date: DateTime.parse(json['date'] as String),
    level: MoodLevel.fromValue(json['mood'] as int),
    feelings: (json['feelings'] as List? ?? const []).cast<String>(),
    note: json['note'] as String?,
  );
}

abstract final class MoodSummaryModel {
  static MoodSummary fromJson(Map<String, dynamic> json) => MoodSummary(
    average: (json['average'] as num?)?.toDouble(),
    streak: json['streak'] as int? ?? 0,
    lowStreak: json['low_streak'] as int? ?? 0,
    checkedInToday: json['checked_in_today'] as bool? ?? false,
    suggestion: MoodSuggestion.fromValue(json['suggestion'] as String?),
  );
}

abstract final class MoodJournalModel {
  /// Réponse de `GET /moods` : `{ data: [...], summary: {...} }`.
  static MoodJournal fromJson(Map<String, dynamic> json) => MoodJournal(
    entries: (json['data'] as List).map((e) => MoodEntryModel.fromJson(e as Map<String, dynamic>)).toList(),
    summary: MoodSummaryModel.fromJson(json['summary'] as Map<String, dynamic>),
  );
}
