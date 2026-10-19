import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:get/get.dart';
import 'package:intl/date_symbol_data_local.dart';

import 'app/core/services/realtime_service.dart';
import 'app/core/services/session_service.dart';
import 'app/core/theme/app_theme.dart';
import 'routes/app_pages.dart';
import 'routes/app_routes.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('fr_FR');
  await SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp]);

  Get
    ..put(SessionService(), permanent: true)
    ..put(RealtimeService(), permanent: true);

  runApp(const SunoogoApp());
}

class SunoogoApp extends StatelessWidget {
  const SunoogoApp({super.key});

  @override
  Widget build(BuildContext context) {
    return GetMaterialApp(
      title: 'SUNOOGO',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light,
      locale: const Locale('fr', 'FR'),
      supportedLocales: const [Locale('fr', 'FR')],
      localizationsDelegates: GlobalMaterialLocalizations.delegates,
      initialRoute: AppRoutes.splash,
      getPages: AppPages.pages,
      defaultTransition: Transition.cupertino,
    );
  }
}
