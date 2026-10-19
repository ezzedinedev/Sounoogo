/// Personne ou service à appeler en cas de crise.
class SafetyContact {
  const SafetyContact({required this.name, required this.phone});

  final String name;
  final String phone;
}

/// Plan de sécurité personnel (modèle de Stanley et Brown), préparé à tête reposée
/// pour être suivi pas à pas pendant une crise.
class SafetyPlan {
  const SafetyPlan({
    this.warningSigns = const [],
    this.copingStrategies = const [],
    this.distractions = const [],
    this.supportContacts = const [],
    this.professionalContacts = const [],
    this.safeEnvironment = const [],
    this.reasonsToLive = const [],
    this.updatedAt,
  });

  /// 1. Pensées, émotions, situations qui annoncent une crise.
  final List<String> warningSigns;

  /// 2. Ce que je peux faire seul·e pour m'apaiser.
  final List<String> copingStrategies;

  /// 3. Lieux et personnes qui me changent les idées.
  final List<String> distractions;

  /// 4. Proches à qui demander de l'aide.
  final List<SafetyContact> supportContacts;

  /// 5. Professionnels et services à contacter.
  final List<SafetyContact> professionalContacts;

  /// 6. Ce que je fais pour rendre mon environnement plus sûr.
  final List<String> safeEnvironment;

  /// Ce qui compte pour moi et me donne envie de tenir.
  final List<String> reasonsToLive;

  final DateTime? updatedAt;

  bool get isEmpty =>
      warningSigns.isEmpty &&
      copingStrategies.isEmpty &&
      distractions.isEmpty &&
      supportContacts.isEmpty &&
      professionalContacts.isEmpty &&
      safeEnvironment.isEmpty &&
      reasonsToLive.isEmpty;
}
