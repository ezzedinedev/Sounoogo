import '../../domain/entities/exercise.dart';

abstract final class ExerciseModel {
  static Exercise fromJson(Map<String, dynamic> json) {
    final breathing = json['breathing'] as Map<String, dynamic>?;
    return Exercise(
      slug: json['slug'] as String,
      title: json['title'] as String,
      summary: json['summary'] as String,
      category: json['category'] as String,
      categoryLabel: json['category_label'] as String? ?? '',
      durationMinutes: json['duration_minutes'] as int? ?? 3,
      steps: (json['steps'] as List? ?? const [])
          .map((s) => ExerciseStep(text: (s as Map<String, dynamic>)['text'] as String, seconds: s['seconds'] as int? ?? 30))
          .toList(),
      breathing: breathing == null
          ? null
          : BreathingPattern(
              inhale: breathing['inhale'] as int,
              hold: breathing['hold'] as int? ?? 0,
              exhale: breathing['exhale'] as int,
              rest: breathing['rest'] as int? ?? 0,
              cycles: breathing['cycles'] as int? ?? 8,
            ),
      audioUrl: json['audio_url'] as String?,
      language: json['language'] as String? ?? 'fr',
    );
  }
}
