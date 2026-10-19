import '../../domain/entities/support_resource.dart';

abstract final class ResourceDirectoryModel {
  static ResourceDirectory fromJson(Map<String, dynamic> json, {bool fromCache = false}) => ResourceDirectory(
    resources: (json['data'] as List).map((r) => _resource(r as Map<String, dynamic>)).toList(),
    cities: (json['cities'] as List? ?? const []).cast<String>(),
    categories: (json['categories'] as List? ?? const [])
        .map((c) => ResourceCategory((c as Map<String, dynamic>)['value'] as String, c['label'] as String))
        .toList(),
    fromCache: fromCache,
  );

  static SupportResource _resource(Map<String, dynamic> json) => SupportResource(
    id: json['id'] as int,
    name: json['name'] as String,
    category: json['category'] as String,
    categoryLabel: json['category_label'] as String? ?? '',
    city: json['city'] as String,
    address: json['address'] as String?,
    phone: json['phone'] as String?,
    website: json['website'] as String?,
    description: json['description'] as String?,
    openingHours: json['opening_hours'] as String?,
    isFree: json['is_free'] as bool? ?? false,
    latitude: (json['latitude'] as num?)?.toDouble(),
    longitude: (json['longitude'] as num?)?.toDouble(),
  );
}
