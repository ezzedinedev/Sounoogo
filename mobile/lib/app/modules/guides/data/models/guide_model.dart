import '../../domain/entities/guide.dart';

abstract final class GuideModel {
  static Guide fromJson(Map<String, dynamic> json) => Guide(
    slug: json['slug'] as String,
    title: json['title'] as String,
    summary: json['summary'] as String,
    readingMinutes: json['reading_minutes'] as int? ?? 1,
    body: json['body'] as String?,
  );
}
