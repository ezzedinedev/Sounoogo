/// Offre d'abonnement (accès aux formations, réduction sur les consultations).
class Plan {
  const Plan({
    required this.code,
    required this.name,
    required this.price,
    required this.durationDays,
    required this.consultationDiscount,
    required this.features,
    this.description,
  });

  final String code;
  final String name;

  /// Prix en francs CFA.
  final int price;
  final int durationDays;

  /// Réduction sur les consultations, en pourcentage.
  final int consultationDiscount;
  final List<String> features;
  final String? description;
}
