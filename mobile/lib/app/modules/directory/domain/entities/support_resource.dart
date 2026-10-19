/// Ressource de proximité vérifiée par l'équipe (centre de santé, association…).
class SupportResource {
  const SupportResource({
    required this.id,
    required this.name,
    required this.category,
    required this.categoryLabel,
    required this.city,
    this.address,
    this.phone,
    this.website,
    this.description,
    this.openingHours,
    this.isFree = false,
    this.latitude,
    this.longitude,
  });

  final int id;
  final String name;
  final String category;
  final String categoryLabel;
  final String city;
  final String? address;
  final String? phone;
  final String? website;
  final String? description;
  final String? openingHours;
  final bool isFree;
  final double? latitude;
  final double? longitude;

  bool get isOnMap => latitude != null && longitude != null;
}

class ResourceCategory {
  const ResourceCategory(this.value, this.label);

  final String value;
  final String label;
}

/// Résultat d'une recherche dans l'annuaire.
class ResourceDirectory {
  const ResourceDirectory({required this.resources, required this.cities, required this.categories, this.fromCache = false});

  final List<SupportResource> resources;

  /// Villes couvertes par l'annuaire.
  final List<String> cities;
  final List<ResourceCategory> categories;
  final bool fromCache;
}
