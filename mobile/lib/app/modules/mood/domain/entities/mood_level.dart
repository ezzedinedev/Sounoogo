/// Échelle d'humeur du check-in, de 1 (très difficile) à 5 (très bien).
enum MoodLevel {
  veryLow(1, 'Très difficile'),
  low(2, 'Difficile'),
  neutral(3, 'Moyen'),
  good(4, 'Bien'),
  veryGood(5, 'Très bien');

  const MoodLevel(this.value, this.label);

  final int value;
  final String label;

  bool get isLow => value <= low.value;

  static MoodLevel fromValue(int value) => values.firstWhere((level) => level.value == value, orElse: () => neutral);
}
