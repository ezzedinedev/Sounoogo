import 'enrollment.dart';
import 'learning_badge.dart';

/// Résultat d'une leçon terminée : la progression à jour et les jalons obtenus à cette occasion.
class LessonCompletion {
  const LessonCompletion({required this.enrollment, required this.newBadges});

  final Enrollment enrollment;
  final List<LearningBadge> newBadges;
}
