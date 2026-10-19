/// Jalon de progression dans les formations (aucun ne se perd).
class LearningBadge {
  const LearningBadge({required this.code, required this.label, required this.description, required this.earned, this.awardedAt});

  final String code;
  final String label;
  final String description;
  final bool earned;
  final DateTime? awardedAt;
}
