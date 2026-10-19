import 'package:get/get.dart';

import '../../../../routes/app_routes.dart';
import '../../../core/models/user_model.dart';
import '../../../core/network/api_exception.dart';
import '../../../core/services/session_service.dart';
import '../repositories/profile_repository.dart';

class ProfileController extends GetxController {
  ProfileController(this._repository);

  final ProfileRepository _repository;
  final SessionService _session = Get.find();

  final isLoading = false.obs;
  final errorMessage = RxnString();

  Rxn<UserModel> get user => _session.user;

  Future<void> upgrade({required String login, required String password}) async {
    isLoading.value = true;
    errorMessage.value = null;
    final isEmail = login.contains('@');
    try {
      await _repository.upgrade(email: isEmail ? login.trim() : null, phone: isEmail ? null : login.trim(), password: password);
      await _session.refreshUser();
      Get.back<void>();
      Get.snackbar('Compte sécurisé', 'Tu peux maintenant te connecter avec tes identifiants.', snackPosition: SnackPosition.BOTTOM);
    } on ApiException catch (error) {
      errorMessage.value = error.message;
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
      await _repository.deleteAccount();
      await _session.signOut(remote: false);
      Get.offAllNamed(AppRoutes.welcome);
      Get.snackbar('Compte supprimé', 'Toutes tes données ont été effacées.', snackPosition: SnackPosition.BOTTOM);
    } on ApiException catch (error) {
      Get.snackbar('Suppression impossible', error.message, snackPosition: SnackPosition.BOTTOM);
    }
  }
}
