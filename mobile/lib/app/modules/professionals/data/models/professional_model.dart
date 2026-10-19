import '../../domain/entities/consultation_mode.dart';
import '../../domain/entities/professional.dart';
import '../../domain/entities/slot.dart';

abstract final class SlotModel {
  static Slot fromJson(Map<String, dynamic> json) => Slot(
    id: json['id'] as int,
    startsAt: DateTime.parse(json['starts_at'] as String).toLocal(),
    endsAt: DateTime.parse(json['ends_at'] as String).toLocal(),
  );
}

abstract final class ProfessionalModel {
  static Professional fromJson(Map<String, dynamic> json) => Professional(
    id: json['id'] as int,
    name: json['name'] as String,
    title: json['title'] as String,
    specialty: json['specialty'] as String,
    sessionPrice: json['session_price'] as int,
    sessionMinutes: json['session_minutes'] as int,
    modes: (json['modes'] as List)
        .map((m) => ConsultationMode((m as Map<String, dynamic>)['value'] as String, m['label'] as String))
        .toList(),
    languages: (json['languages'] as List? ?? const []).cast<String>(),
    bio: json['bio'] as String?,
    city: json['city'] as String?,
    photoUrl: json['photo_url'] as String?,
    slots: (json['next_slots'] as List? ?? const []).map((s) => SlotModel.fromJson(s as Map<String, dynamic>)).toList(),
  );
}
