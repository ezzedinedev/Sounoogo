/// Étape minutée d'un exercice guidé.
class ExerciseStep {
  const ExerciseStep({required this.text, required this.seconds});

  final String text;
  final int seconds;
}

/// Rythme d'une respiration guidée, en secondes.
class BreathingPattern {
  const BreathingPattern({required this.inhale, required this.exhale, required this.cycles, this.hold = 0, this.rest = 0});

  /// Respiration apaisante par défaut : l'expiration plus longue que l'inspiration aide le corps à se calmer.
  static const calming = BreathingPattern(inhale: 4, hold: 2, exhale: 6, cycles: 8);

  final int inhale;
  final int hold;
  final int exhale;
  final int rest;
  final int cycles;

  int get cycleSeconds => inhale + hold + exhale + rest;

  Duration get total => Duration(seconds: cycleSeconds * cycles);
}

/// Exercice guidé : des étapes minutées, ou une respiration rythmée. Utilisable hors ligne.
class Exercise {
  const Exercise({
    required this.slug,
    required this.title,
    required this.summary,
    required this.category,
    required this.categoryLabel,
    required this.durationMinutes,
    this.steps = const [],
    this.breathing,
    this.audioUrl,
    this.language = 'fr',
  });

  final String slug;
  final String title;
  final String summary;
  final String category;
  final String categoryLabel;
  final int durationMinutes;
  final List<ExerciseStep> steps;
  final BreathingPattern? breathing;

  /// Voix qui guide l'exercice, facultative.
  final String? audioUrl;
  final String language;

  bool get isBreathing => breathing != null;
}
