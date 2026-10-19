import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../domain/entities/safety_plan.dart';
import '../../domain/usecases/delete_safety_plan.dart';
import '../../domain/usecases/get_safety_plan.dart';
import '../../domain/usecases/save_safety_plan.dart';
import '../../../../core/widgets/app_toast.dart';

class SafetyPlanController extends GetxController {
  SafetyPlanController({required this._getSafetyPlan, required this._saveSafetyPlan, required this._deleteSafetyPlan});

  final GetSafetyPlan _getSafetyPlan;
  final SaveSafetyPlan _saveSafetyPlan;
  final DeleteSafetyPlan _deleteSafetyPlan;

  final plan = Rxn<SafetyPlan>();
  final isLoading = false.obs;
  final isSaving = false.obs;
  final errorMessage = RxnString();

  /// Vrai une fois le premier chargement terminé (plan présent ou non).
  final isLoaded = false.obs;

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      plan.value = await _getSafetyPlan();
      isLoaded.value = true;
    } on Failure catch (failure) {
      errorMessage.value = failure is NetworkFailure
          ? 'Pas de connexion, et ton plan n\'a pas encore été enregistré sur ce téléphone.'
          : failure.message;
    } finally {
      isLoading.value = false;
    }
  }

  Future<bool> save(SafetyPlan draft) async {
    isSaving.value = true;
    try {
      plan.value = await _saveSafetyPlan(draft);
      isLoaded.value = true;
      return true;
    } on Failure catch (failure) {
      AppToast.show('Plan non enregistré', failure.message);
      return false;
    } finally {
      isSaving.value = false;
    }
  }

  Future<void> delete() async {
    try {
      await _deleteSafetyPlan();
      plan.value = null;
    } on Failure catch (failure) {
      AppToast.show('Suppression impossible', failure.message);
    }
  }
}
