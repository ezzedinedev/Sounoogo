import 'mood_level.dart';

class MoodEntry {
  const MoodEntry({required this.date, required this.level, this.feelings = const [], this.note});

  final DateTime date;
  final MoodLevel level;
  final List<String> feelings;
  final String? note;
}
