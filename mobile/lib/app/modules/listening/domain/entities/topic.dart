/// Thèmes proposés à l'ouverture d'une conversation (mêmes valeurs que l'API).
class Topic {
  const Topic(this.value, this.label);

  final String value;
  final String label;

  static const all = [
    Topic('stress', 'Stress'),
    Topic('anxiety', 'Anxiété'),
    Topic('sadness', 'Tristesse'),
    Topic('relationship', 'Relation'),
    Topic('family', 'Famille'),
    Topic('grief', 'Deuil'),
    Topic('self_esteem', 'Estime de soi'),
    Topic('violence', 'Violence'),
    Topic('other', 'Autre chose'),
  ];
}
