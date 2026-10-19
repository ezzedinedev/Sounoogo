/// Guide pratique pour soutenir un proche.
class Guide {
  const Guide({required this.slug, required this.title, required this.summary, required this.readingMinutes, this.body});

  final String slug;
  final String title;
  final String summary;
  final int readingMinutes;

  /// Texte en Markdown, chargé à l'ouverture du guide.
  final String? body;
}
