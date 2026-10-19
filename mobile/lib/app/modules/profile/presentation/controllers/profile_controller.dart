import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/error/failure.dart';
import '../../../../shared/domain/entities/user.dart';
import '../../../../shared/presentation/session_service.dart';
import '../../domain/usecases/delete_account.dart';
import '../../domain/usecases/secure_account.dart';
import '../../../../core/widgets/app_toast.dart';

class ProfileController extends GetxController {
  ProfileController({required this._secureAccount, required this._deleteAccount});

  final SecureAccount _secureAccount;
  final DeleteAccount _deleteAccount;
  final SessionService _session = Get.find();

  final isLoading = false.obs;
  final errorMessage = RxnString();

  Rxn<User> get user => _session.user;

  Future<void> upgrade({required String login, required String password}) async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      _session.updateUser(await _secureAccount(login: login, password: password));
      Get.back<void>();
      AppToast.show('Compte sécurisé', 'Tu peux maintenant te connecter avec tes identifiants.');
    } on Failure catch (failure) {
      errorMessage.value = failure.message;
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> signOut() async {
    await _session.signOut();
    Get.offAllNamed(AppRoutes.welcome);
  }

  Future<void> deleteAccount() async {
    try {
      await _deleteAccount();
      await _session.signOut(remote: false);
      Get.offAllNamed(AppRoutes.welcome);
      AppToast.show('Compte supprimé', 'Toutes tes données ont été effacées.');
    } on Failure catch (failure) {
      AppToast.show('Suppression impossible', failure.message);
    }
  }
}
