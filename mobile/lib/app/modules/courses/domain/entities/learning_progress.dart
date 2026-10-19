import 'certificate.dart';
import 'learning_badge.dart';

class LearningProgress {
  const LearningProgress({
    required this.lessons,
    required this.courses,
    required this.currentStreak,
    required this.bestStreak,
    required this.activeToday,
    required this.badges,
    required this.certificates,
  });

  final int lessons;
  final int courses;

  /// Jours d'affilée avec au moins une leçon terminée (encore valable si la dernière date d'hier).
  final int currentStreak;
  final int bestStreak;
  final bool activeToday;
  final List<LearningBadge> badges;
  final List<Certificate> certificates;

  int get earnedCount => badges.where((badge) => badge.earned).length;

  Certificate? certificateFor(String courseSlug) {
    for (final certificate in certificates) {
      if (certificate.courseSlug == courseSlug) return certificate;
    }
    return null;
  }
}
