/// Présentiel, téléphone ou visio.
class ConsultationMode {
  const ConsultationMode(this.value, this.label);

  final String value;
  final String label;

  static const filters = {'in_person': 'Présentiel', 'phone': 'Téléphone', 'video': 'Visio'};
}
