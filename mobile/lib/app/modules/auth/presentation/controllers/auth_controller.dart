import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/error/failure.dart';
import '../../../../shared/presentation/session_service.dart';
import '../../domain/entities/auth_session.dart';
import '../../domain/usecases/recover_account.dart';
import '../../domain/usecases/sign_in.dart';
import '../../domain/usecases/start_anonymous_session.dart';

class AuthController extends GetxController {
  AuthController({required this._startAnonymousSession, required this._recoverAccount, required this._signIn});

  final StartAnonymousSession _startAnonymousSession;
  final RecoverAccount _recoverAccount;
  final SignIn _signIn;
  final SessionService _session = Get.find();

  final isLoading = false.obs;
  final errorMessage = RxnString();

  /// Code de récupération à montrer une seule fois après l'accès anonyme.
  final pendingResult = Rxn<AuthSession>();
  final codeSaved = false.obs;

  Future<void> startAnonymous() => _run(() async {
    pendingResult.value = await _startAnonymousSession();
    Get.toNamed(AppRoutes.recoveryCode);
  });

  /// Le membre confirme avoir noté son code : on ouvre la session.
  Future<void> confirmRecoveryCode() async {
    final result = pendingResult.value;
    if (result == null) {
      return;
    }
    await _open(result);
    pendingResult.value = null;
  }

  Future<void> recover(String pseudonym, String code) =>
      _run(() async => _open(await _recoverAccount(pseudonym: pseudonym, recoveryCode: code)));

  Future<void> login(String login, String password) => _run(() async => _open(await _signIn(login: login, password: password)));

  Future<void> _open(AuthSession session) async {
    await _session.open(session.token, session.user);
    Get.offAllNamed(AppRoutes.home);
  }

  Future<void> _run(Future<void> Function() action) async {
    if (isLoading.value) {
      return;
    }
    isLoading.value = true;
    errorMessage.value = null;
    try {
      await action();
    } on Failure catch (failure) {
      errorMessage.value = failure.message;
    } finally {
      isLoading.value = false;
    }
  }
}
