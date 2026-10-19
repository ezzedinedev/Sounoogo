class SlotModel {
  const SlotModel({required this.id, required this.startsAt, required this.endsAt});

  factory SlotModel.fromJson(Map<String, dynamic> json) => SlotModel(
        id: json['id'] as int,
        startsAt: DateTime.parse(json['starts_at'] as String).toLocal(),
        endsAt: DateTime.parse(json['ends_at'] as String).toLocal(),
      );

  final int id;
  final DateTime startsAt;
  final DateTime endsAt;
}

class ConsultationModeModel {
  const ConsultationModeModel(this.value, this.label);

  factory ConsultationModeModel.fromJson(Map<String, dynamic> json) => ConsultationModeModel(json['value'] as String, json['label'] as String);

  final String value;
  final String label;
}

class ProfessionalModel {
  const ProfessionalModel({
    required this.id,
    required this.name,
    required this.title,
    required this.specialty,
    required this.sessionPrice,
    required this.sessionMinutes,
    required this.modes,
    required this.languages,
    this.bio,
    this.city,
    this.photoUrl,
    this.slots = const [],
  });

  factory ProfessionalModel.fromJson(Map<String, dynamic> json) => ProfessionalModel(
        id: json['id'] as int,
        name: json['name'] as String,
        title: json['title'] as String,
        specialty: json['specialty'] as String,
        sessionPrice: json['session_price'] as int,
        sessionMinutes: json['session_minutes'] as int,
        modes: (json['modes'] as List).map((m) => ConsultationModeModel.fromJson(m as Map<String, dynamic>)).toList(),
        languages: (json['languages'] as List? ?? const []).cast<String>(),
        bio: json['bio'] as String?,
        city: json['city'] as String?,
        photoUrl: json['photo_url'] as String?,
        slots: (json['next_slots'] as List? ?? const []).map((s) => SlotModel.fromJson(s as Map<String, dynamic>)).toList(),
      );

  final int id;
  final String name;
  final String title;
  final String specialty;
  final int sessionPrice;
  final int sessionMinutes;
  final List<ConsultationModeModel> modes;
  final List<String> languages;
  final String? bio;
  final String? city;
  final String? photoUrl;
  final List<SlotModel> slots;

  String get initials => name.replaceFirst(RegExp(r'^(Dr|Pr)\.?\s+'), '').split(' ').take(2).map((p) => p.isEmpty ? '' : p[0]).join();
}
