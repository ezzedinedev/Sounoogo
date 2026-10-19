/// Émotions proposées au check-in (mêmes valeurs que l'API).
class Feeling {
  const Feeling(this.value, this.label);

  final String value;
  final String label;

  static const all = [
    Feeling('calm', 'Calme'),
    Feeling('joy', 'Joie'),
    Feeling('gratitude', 'Gratitude'),
    Feeling('tired', 'Fatigue'),
    Feeling('anxious', 'Anxiété'),
    Feeling('sad', 'Tristesse'),
    Feeling('angry', 'Colère'),
    Feeling('lonely', 'Solitude'),
    Feeling('stressed', 'Stress'),
  ];

  /// Nombre maximal d'émotions par check-in (même limite que l'API).
  static const maxPerCheckIn = 5;

  static String labelOf(String value) => all.where((f) => f.value == value).firstOrNull?.label ?? value;
}
