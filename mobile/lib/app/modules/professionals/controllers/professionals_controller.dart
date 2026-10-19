import 'package:get/get.dart';

import '../../../core/network/api_exception.dart';
import '../models/professional_model.dart';
import '../repositories/professional_repository.dart';

class ProfessionalsController extends GetxController {
  ProfessionalsController(this._repository);

  final ProfessionalRepository _repository;

  final items = <ProfessionalModel>[].obs;
  final isLoading = false.obs;
  final errorMessage = RxnString();
  final mode = RxnString();

  static const modes = {'in_person': 'Présentiel', 'phone': 'Téléphone', 'video': 'Visio'};

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      items.assignAll(await _repository.getAll(mode: mode.value));
    } on ApiException catch (error) {
      errorMessage.value = _friendlyError(error);
    } finally {
      isLoading.value = false;
    }
  }

  void filterBy(String? value) {
    mode.value = value;
    load();
  }

  String _friendlyError(ApiException error) => error.isNetwork ? 'Impossible de charger les professionnels. Vérifie ta connexion.' : error.message;
}
