import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:intl/date_symbol_data_local.dart';

import 'app/core/monitoring/error_reporting.dart';
import 'app/core/l10n/app_locales.dart';
import 'app/core/theme/app_theme.dart';
import 'app/di/app_binding.dart';
import 'app/modules/settings/presentation/widgets/app_frame.dart';
import 'routes/app_pages.dart';
import 'routes/app_routes.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('fr_FR');
  await SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp]);

  await ErrorReporting.run(() => runApp(const SunoogoApp()));
}

class SunoogoApp extends StatelessWidget {
  const SunoogoApp({super.key});

  @override
  Widget build(BuildContext context) {
    return GetMaterialApp(
      title: 'SUNOOGO',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light,
      locale: AppLocales.fallback,
      fallbackLocale: AppLocales.fallback,
      supportedLocales: AppLocales.supported,
      localizationsDelegates: AppLocales.delegates,
      initialBinding: AppBinding(),
      builder: (context, child) => AppFrame(child: child ?? const SizedBox.shrink()),
      initialRoute: AppRoutes.splash,
      getPages: AppPages.pages,
      defaultTransition: Transition.cupertino,
    );
  }
}
