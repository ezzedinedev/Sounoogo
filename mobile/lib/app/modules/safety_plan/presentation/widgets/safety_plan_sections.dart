import 'package:flutter/material.dart';

import '../../domain/entities/safety_plan.dart';

/// Étapes du plan, dans l'ordre où on les suit pendant une crise.
enum SafetyPlanSection {
  warningSigns(
    1,
    Icons.visibility_outlined,
    'Mes signaux d\'alerte',
    'Ce que je ressens ou pense quand ça commence à aller mal.',
    'Ex. : je n\'arrive plus à dormir',
  ),
  copingStrategies(
    2,
    Icons.self_improvement_rounded,
    'Ce qui m\'apaise',
    'Ce que je peux faire seul·e pour aller un peu mieux.',
    'Ex. : marcher, écouter de la musique',
  ),
  distractions(
    3,
    Icons.groups_2_outlined,
    'Lieux et personnes qui me changent les idées',
    'Là où je peux aller, ceux avec qui passer du temps.',
    'Ex. : aller chez ma cousine',
  ),
  supportContacts(
    4,
    Icons.favorite_border_rounded,
    'Les proches que je peux appeler',
    'Des personnes de confiance à qui demander de l\'aide.',
    '',
  ),
  professionalContacts(
    5,
    Icons.medical_services_outlined,
    'Les professionnels à contacter',
    'Mon psychologue, un centre de santé… Les numéros d\'urgence sont toujours ajoutés.',
    '',
  ),
  safeEnvironment(
    6,
    Icons.shield_outlined,
    'Rendre mon environnement plus sûr',
    'Ce que je fais pour m\'éloigner de ce qui pourrait me mettre en danger.',
    'Ex. : confier mes médicaments à un proche',
  ),
  reasonsToLive(
    7,
    Icons.wb_sunny_outlined,
    'Mes raisons de tenir',
    'Ce qui compte pour moi, ce qui me donne envie de continuer.',
    'Ex. : mon petit frère, mes projets',
  );

  const SafetyPlanSection(this.step, this.icon, this.title, this.help, this.hint);

  final int step;
  final IconData icon;
  final String title;
  final String help;
  final String hint;

  bool get isContacts => this == supportContacts || this == professionalContacts;

  List<String> textsOf(SafetyPlan plan) => switch (this) {
    warningSigns => plan.warningSigns,
    copingStrategies => plan.copingStrategies,
    distractions => plan.distractions,
    safeEnvironment => plan.safeEnvironment,
    reasonsToLive => plan.reasonsToLive,
    supportContacts || professionalContacts => const [],
  };

  List<SafetyContact> contactsOf(SafetyPlan plan) => switch (this) {
    supportContacts => plan.supportContacts,
    professionalContacts => plan.professionalContacts,
    _ => const [],
  };
}
