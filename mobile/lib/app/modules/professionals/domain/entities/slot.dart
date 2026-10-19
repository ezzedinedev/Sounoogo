/// Créneau libre d'un professionnel.
class Slot {
  const Slot({required this.id, required this.startsAt, required this.endsAt});

  final int id;
  final DateTime startsAt;
  final DateTime endsAt;
}
