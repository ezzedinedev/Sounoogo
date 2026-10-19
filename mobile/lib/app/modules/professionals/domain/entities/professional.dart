import 'consultation_mode.dart';
import 'slot.dart';

/// Professionnel partenaire vérifié (psychologue, coach…).
class Professional {
  const Professional({
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

  final int id;
  final String name;
  final String title;
  final String specialty;

  /// Prix d'une séance en francs CFA.
  final int sessionPrice;
  final int sessionMinutes;
  final List<ConsultationMode> modes;
  final List<String> languages;
  final String? bio;
  final String? city;
  final String? photoUrl;

  /// Prochains créneaux libres (renseignés sur la fiche détaillée).
  final List<Slot> slots;

  String get initials => name.replaceFirst(RegExp(r'^(Dr|Pr)\.?\s+'), '').split(' ').take(2).map((p) => p.isEmpty ? '' : p[0]).join();
}
