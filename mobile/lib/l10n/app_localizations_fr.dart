// ignore: unused_import
import 'package:intl/intl.dart' as intl;

import 'app_localizations.dart';

// ignore_for_file: type=lint

/// The translations for French (`fr`).
class AppLocalizationsFr extends AppLocalizations {
  AppLocalizationsFr([String locale = 'fr']) : super(locale);

  @override
  String get navHome => 'Accueil';

  @override
  String get navListening => 'Écoute';

  @override
  String get navProfessionals => 'Pros';

  @override
  String get navCourses => 'Parcours';

  @override
  String get navMe => 'Moi';

  @override
  String get actionRetry => 'Réessayer';

  @override
  String get actionCancel => 'Annuler';

  @override
  String get actionContinue => 'Continuer';

  @override
  String get errorTitle => 'Oups';

  @override
  String get sosButton => 'SOS';

  @override
  String get emergencyTitle => 'Tu n\'es pas seul·e';

  @override
  String get emergencySupervisorAlerted =>
      'Un superviseur SUNOOGO vient d\'être prévenu. Si tu es en danger immédiat, appelle maintenant :';

  @override
  String get emergencyCallNow => 'Si tu es en danger immédiat, appelle maintenant :';

  @override
  String get emergencyMyPlan => 'Mon plan';

  @override
  String get emergencyBreathe => 'Respirer';
}
