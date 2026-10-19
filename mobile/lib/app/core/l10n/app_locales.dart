import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';

import '../../../l10n/app_localizations.dart';

/// Langues de l'app. Le français est la référence ; le mooré (mos) et le dioula (dyu) seront ajoutés
/// une fois les fichiers ARB traduits et relus par des locuteurs natifs.
abstract final class AppLocales {
  static const fallback = Locale('fr', 'FR');

  static const supported = [fallback];

  static const delegates = [
    AppLocalizations.delegate,
    GlobalMaterialLocalizations.delegate,
    GlobalWidgetsLocalizations.delegate,
    GlobalCupertinoLocalizations.delegate,
  ];
}

/// Accès court aux textes traduits : `context.l10n.actionRetry`.
extension AppLocalizationsContext on BuildContext {
  AppLocalizations get l10n => AppLocalizations.of(this);
}
