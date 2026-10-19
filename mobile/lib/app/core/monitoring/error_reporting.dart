import 'dart:async';

import 'package:sentry_flutter/sentry_flutter.dart';

/// Suivi des plantages (Sentry), configuré au build : --dart-define=SENTRY_DSN=…
/// Sans DSN, rien n'est envoyé. Avec, seuls la trace technique et l'écran courant partent :
/// ni texte saisi, ni contenu affiché, ni capture d'écran, ni identité.
abstract final class ErrorReporting {
  static const _dsn = String.fromEnvironment('SENTRY_DSN');
  static const _environment = String.fromEnvironment('SENTRY_ENVIRONMENT', defaultValue: 'production');

  static bool get enabled => _dsn.isNotEmpty;

  static Future<void> run(FutureOr<void> Function() appRunner) async {
    if (!enabled) {
      await appRunner();
      return;
    }

    await SentryFlutter.init((options) {
      options
        ..dsn = _dsn
        ..environment = _environment
        ..sendDefaultPii = false
        ..attachScreenshot = false
        ..maxRequestBodySize = MaxRequestBodySize.never
        ..enableUserInteractionBreadcrumbs = false
        ..enablePrintBreadcrumbs = false
        ..tracesSampleRate = 0;
      // Volontairement : aucun Sentry.configureScope(setUser) dans l'app, et pas d'intégration Dio
      // (les requêtes, donc les messages, ne sont jamais attachées aux événements).
    }, appRunner: appRunner);
  }
}
