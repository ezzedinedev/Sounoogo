import 'dart:async';

import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../shared/presentation/session_service.dart';

class SplashController extends GetxController {
  final SessionService _session = Get.find();
  final _animationDone = Completer<void>();

  /// Appelé par l'écran quand l'animation du logo est terminée.
  void onAnimationComplete() {
    if (!_animationDone.isCompleted) {
      _animationDone.complete();
    }
  }

  @override
  Future<void> onReady() async {
    super.onReady();
    // On attend à la fois la fin de l'animation et la restauration de la session.
    await Future.wait([_session.restore(), _animationDone.future]);
    Get.offAllNamed(_session.isSignedIn ? AppRoutes.home : AppRoutes.welcome);
  }
}
