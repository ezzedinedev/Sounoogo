/// Taille du texte choisie dans l'app, appliquée en plus du réglage du téléphone.
enum TextSize {
  normal(1.0, 'Normale'),
  large(1.15, 'Grande'),
  extraLarge(1.3, 'Très grande');

  const TextSize(this.factor, this.label);

  final double factor;
  final String label;

  static TextSize fromName(String? name) => values.asNameMap()[name] ?? normal;
}

class DisplaySettings {
  const DisplaySettings({this.textSize = TextSize.normal});

  final TextSize textSize;
}
