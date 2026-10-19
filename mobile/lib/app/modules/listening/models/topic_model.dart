/// Thèmes proposés à l'ouverture d'une conversation (mêmes valeurs que l'API).
class TopicModel {
  const TopicModel(this.value, this.label);

  final String value;
  final String label;

  static const all = [
    TopicModel('stress', 'Stress'),
    TopicModel('anxiety', 'Anxiété'),
    TopicModel('sadness', 'Tristesse'),
    TopicModel('relationship', 'Relation'),
    TopicModel('family', 'Famille'),
    TopicModel('grief', 'Deuil'),
    TopicModel('self_esteem', 'Estime de soi'),
    TopicModel('violence', 'Violence'),
    TopicModel('other', 'Autre chose'),
  ];
}
