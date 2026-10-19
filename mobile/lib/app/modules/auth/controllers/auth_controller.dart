import 'package:get/get.dart';

import '../../../../routes/app_routes.dart';
import '../../../core/network/api_exception.dart';
import '../../../core/services/session_service.dart';
import '../models/auth_result_model.dart';
import '../repositories/auth_repository.dart';

class AuthController extends GetxController {
  AuthController(this._repository);

  final AuthRepository _repository;
  final SessionService _session = Get.find();

  final isLoading = false.obs;
  final errorMessage = RxnString();

  /// Code de récupération à montrer une seule fois après l'accès anonyme.
  final pendingResult = Rxn<AuthResultModel>();
  final codeSaved = false.obs;

  Future<void> startAnonymous() => _run(() async {
        pendingResult.value = await _repository.startAnonymous();
        Get.toNamed(AppRoutes.recoveryCode);
      });

  /// Le membre confirme avoir noté son code : on ouvre la session.
  Future<void> confirmRecoveryCode() async {
    final result = pendingResult.value;
    if (result == null) {
      return;
    }
    await _session.signIn(result.token, result.user);
    pendingResult.value = null;
    Get.offAllNamed(AppRoutes.home);
  }

  Future<void> recover(String pseudonym, String code) => _run(() async {
        final result = await _repository.recover(pseudonym: pseudonym.trim(), code: code.trim());
        await _session.signIn(result.token, result.user);
        Get.offAllNamed(AppRoutes.home);
      });

  Future<void> login(String login, String password) => _run(() async {
        final result = await _repository.login(login: login.trim(), password: password);
        await _session.signIn(result.token, result.user);
        Get.offAllNamed(AppRoutes.home);
      });

  Future<void> _run(Future<void> Function() action) async {
    if (isLoading.value) {
      return;
    }
    isLoading.value = true;
    errorMessage.value = null;
    try {
      await action();
    } on ApiException catch (error) {
      errorMessage.value = _friendlyError(error);
    } finally {
      isLoading.value = false;
    }
  }

  String _friendlyError(ApiException error) => error.message;
}
