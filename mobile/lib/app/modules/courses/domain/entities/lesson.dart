class Lesson {
  const Lesson({
    required this.id,
    required this.title,
    required this.position,
    required this.durationMinutes,
    required this.isPreview,
    this.content,
    this.videoUrl,
  });

  final int id;
  final String title;
  final int position;
  final int durationMinutes;

  /// Leçon consultable sans inscription.
  final bool isPreview;

  /// Contenu Markdown, présent seulement sur la leçon ouverte.
  final String? content;
  final String? videoUrl;
}
