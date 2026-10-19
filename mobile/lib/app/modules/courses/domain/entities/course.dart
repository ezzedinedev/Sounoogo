import 'lesson.dart';

/// Parcours de confiance en soi (niveau 3).
class Course {
  const Course({
    required this.slug,
    required this.title,
    required this.summary,
    required this.levelLabel,
    required this.price,
    required this.includedInSubscription,
    this.description,
    this.coverUrl,
    this.lessonsCount,
    this.durationMinutes,
    this.lessons = const [],
  });

  final String slug;
  final String title;
  final String summary;
  final String levelLabel;

  /// Prix à l'unité en francs CFA ; 0 pour une formation gratuite.
  final int price;
  final bool includedInSubscription;
  final String? description;
  final String? coverUrl;
  final int? lessonsCount;
  final int? durationMinutes;
  final List<Lesson> lessons;

  bool get isFree => price == 0;
}
