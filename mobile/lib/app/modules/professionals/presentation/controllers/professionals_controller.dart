import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../domain/entities/consultation_mode.dart';
import '../../domain/entities/professional.dart';
import '../../domain/usecases/get_professionals.dart';

class ProfessionalsController extends GetxController {
  ProfessionalsController(this._getProfessionals);

  final GetProfessionals _getProfessionals;

  final items = <Professional>[].obs;
  final isLoading = false.obs;
  final errorMessage = RxnString();
  final mode = RxnString();

  Map<String, String> get modes => ConsultationMode.filters;

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      items.assignAll(await _getProfessionals(mode: mode.value));
    } on Failure catch (failure) {
      errorMessage.value = _friendlyError(failure);
    } finally {
      isLoading.value = false;
    }
  }

  void filterBy(String? value) {
    mode.value = value;
    load();
  }

  String _friendlyError(Failure failure) =>
      failure is NetworkFailure ? 'Impossible de charger les professionnels. Vérifie ta connexion.' : failure.message;
}
