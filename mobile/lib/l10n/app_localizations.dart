import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:intl/intl.dart' as intl;

import 'app_localizations_fr.dart';

// ignore_for_file: type=lint

/// Callers can lookup localized strings with an instance of AppLocalizations
/// returned by `AppLocalizations.of(context)`.
///
/// Applications need to include `AppLocalizations.delegate()` in their app's
/// `localizationDelegates` list, and the locales they support in the app's
/// `supportedLocales` list. For example:
///
/// ```dart
/// import 'l10n/app_localizations.dart';
///
/// return MaterialApp(
///   localizationsDelegates: AppLocalizations.localizationsDelegates,
///   supportedLocales: AppLocalizations.supportedLocales,
///   home: MyApplicationHome(),
/// );
/// ```
///
/// ## Update pubspec.yaml
///
/// Please make sure to update your pubspec.yaml to include the following
/// packages:
///
/// ```yaml
/// dependencies:
///   # Internationalization support.
///   flutter_localizations:
///     sdk: flutter
///   intl: any # Use the pinned version from flutter_localizations
///
///   # Rest of dependencies
/// ```
///
/// ## iOS Applications
///
/// iOS applications define key application metadata, including supported
/// locales, in an Info.plist file that is built into the application bundle.
/// To configure the locales supported by your app, you’ll need to edit this
/// file.
///
/// First, open your project’s ios/Runner.xcworkspace Xcode workspace file.
/// Then, in the Project Navigator, open the Info.plist file under the Runner
/// project’s Runner folder.
///
/// Next, select the Information Property List item, select Add Item from the
/// Editor menu, then select Localizations from the pop-up menu.
///
/// Select and expand the newly-created Localizations item then, for each
/// locale your application supports, add a new item and select the locale
/// you wish to add from the pop-up menu in the Value field. This list should
/// be consistent with the languages listed in the AppLocalizations.supportedLocales
/// property.
abstract class AppLocalizations {
  AppLocalizations(String locale) : localeName = intl.Intl.canonicalizedLocale(locale.toString());

  final String localeName;

  static AppLocalizations of(BuildContext context) {
    return Localizations.of<AppLocalizations>(context, AppLocalizations)!;
  }

  static const LocalizationsDelegate<AppLocalizations> delegate = _AppLocalizationsDelegate();

  /// A list of this localizations delegate along with the default localizations
  /// delegates.
  ///
  /// Returns a list of localizations delegates containing this delegate along with
  /// GlobalMaterialLocalizations.delegate, GlobalCupertinoLocalizations.delegate,
  /// and GlobalWidgetsLocalizations.delegate.
  ///
  /// Additional delegates can be added by appending to this list in
  /// MaterialApp. This list does not have to be used at all if a custom list
  /// of delegates is preferred or required.
  static const List<LocalizationsDelegate<dynamic>> localizationsDelegates = <LocalizationsDelegate<dynamic>>[
    delegate,
    GlobalMaterialLocalizations.delegate,
    GlobalCupertinoLocalizations.delegate,
    GlobalWidgetsLocalizations.delegate,
  ];

  /// A list of this localizations delegate's supported locales.
  static const List<Locale> supportedLocales = <Locale>[Locale('fr')];

  /// Onglet de la barre de navigation.
  ///
  /// In fr, this message translates to:
  /// **'Accueil'**
  String get navHome;

  /// No description provided for @navListening.
  ///
  /// In fr, this message translates to:
  /// **'Écoute'**
  String get navListening;

  /// No description provided for @navProfessionals.
  ///
  /// In fr, this message translates to:
  /// **'Pros'**
  String get navProfessionals;

  /// No description provided for @navCourses.
  ///
  /// In fr, this message translates to:
  /// **'Parcours'**
  String get navCourses;

  /// No description provided for @navMe.
  ///
  /// In fr, this message translates to:
  /// **'Moi'**
  String get navMe;

  /// No description provided for @actionRetry.
  ///
  /// In fr, this message translates to:
  /// **'Réessayer'**
  String get actionRetry;

  /// No description provided for @actionCancel.
  ///
  /// In fr, this message translates to:
  /// **'Annuler'**
  String get actionCancel;

  /// No description provided for @actionContinue.
  ///
  /// In fr, this message translates to:
  /// **'Continuer'**
  String get actionContinue;

  /// No description provided for @errorTitle.
  ///
  /// In fr, this message translates to:
  /// **'Oups'**
  String get errorTitle;

  /// Bouton d'urgence, présent sur tous les écrans. Garder court.
  ///
  /// In fr, this message translates to:
  /// **'SOS'**
  String get sosButton;

  /// No description provided for @emergencyTitle.
  ///
  /// In fr, this message translates to:
  /// **'Tu n\'es pas seul·e'**
  String get emergencyTitle;

  /// No description provided for @emergencySupervisorAlerted.
  ///
  /// In fr, this message translates to:
  /// **'Un superviseur SUNOOGO vient d\'être prévenu. Si tu es en danger immédiat, appelle maintenant :'**
  String get emergencySupervisorAlerted;

  /// No description provided for @emergencyCallNow.
  ///
  /// In fr, this message translates to:
  /// **'Si tu es en danger immédiat, appelle maintenant :'**
  String get emergencyCallNow;

  /// No description provided for @emergencyMyPlan.
  ///
  /// In fr, this message translates to:
  /// **'Mon plan'**
  String get emergencyMyPlan;

  /// No description provided for @emergencyBreathe.
  ///
  /// In fr, this message translates to:
  /// **'Respirer'**
  String get emergencyBreathe;
}

class _AppLocalizationsDelegate extends LocalizationsDelegate<AppLocalizations> {
  const _AppLocalizationsDelegate();

  @override
  Future<AppLocalizations> load(Locale locale) {
    return SynchronousFuture<AppLocalizations>(lookupAppLocalizations(locale));
  }

  @override
  bool isSupported(Locale locale) => <String>['fr'].contains(locale.languageCode);

  @override
  bool shouldReload(_AppLocalizationsDelegate old) => false;
}

AppLocalizations lookupAppLocalizations(Locale locale) {
  // Lookup logic when only language code is specified.
  switch (locale.languageCode) {
    case 'fr':
      return AppLocalizationsFr();
  }

  throw FlutterError(
    'AppLocalizations.delegate failed to load unsupported locale "$locale". This is likely '
    'an issue with the localizations generation tool. Please file an issue '
    'on GitHub with a reproducible sample app and the gen-l10n configuration '
    'that was used.',
  );
}
