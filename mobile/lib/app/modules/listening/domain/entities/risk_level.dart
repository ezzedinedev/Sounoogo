/// Niveau de détresse détecté par le serveur sur une conversation.
enum RiskLevel {
  none,
  medium,
  high;

  static RiskLevel fromValue(String? value) => values.asNameMap()[value] ?? none;
}
