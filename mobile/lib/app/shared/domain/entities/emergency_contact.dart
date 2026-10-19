/// Numéro d'urgence local (police, pompiers, ligne d'écoute…).
class EmergencyContact {
  const EmergencyContact({required this.label, required this.phone, this.description});

  final String label;
  final String phone;
  final String? description;
}
