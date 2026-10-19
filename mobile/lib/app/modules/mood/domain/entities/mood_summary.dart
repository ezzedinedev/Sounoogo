/// Ce que l'app propose selon le journal.
enum MoodSuggestion {
  /// Plusieurs jours difficiles d'affilée : parler à un écoutant.
  talk,

  /// Journée difficile : un exercice d'apaisement.
  exercise,
  none;

  static MoodSuggestion fromValue(String? value) => values.asNameMap()[value] ?? none;
}

class MoodSummary {
  const MoodSummary({required this.streak, required this.lowStreak, required this.checkedInToday, required this.suggestion, this.average});

  static const empty = MoodSummary(streak: 0, lowStreak: 0, checkedInToday: false, suggestion: MoodSuggestion.none);

  /// Moyenne sur la période, de 1 à 5.
  final double? average;

  /// Jours de check-in consécutifs.
  final int streak;
  final int lowStreak;
  final bool checkedInToday;
  final MoodSuggestion suggestion;
}
