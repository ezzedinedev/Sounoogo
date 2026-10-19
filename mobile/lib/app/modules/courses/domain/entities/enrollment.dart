class Enrollment {
  const Enrollment({required this.courseSlug, required this.progress, required this.completedLessonIds});

  final String courseSlug;

  /// Progression en pourcentage.
  final int progress;
  final List<int> completedLessonIds;

  bool get isCompleted => progress >= 100;
}
