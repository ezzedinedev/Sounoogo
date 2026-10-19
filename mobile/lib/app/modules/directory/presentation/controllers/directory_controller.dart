import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../domain/entities/support_resource.dart';
import '../../domain/usecases/search_resources.dart';

class DirectoryController extends GetxController {
  DirectoryController(this._searchResources);

  final SearchResources _searchResources;

  final directory = Rxn<ResourceDirectory>();
  final city = RxnString();
  final category = RxnString();
  final showMap = false.obs;
  final selected = Rxn<SupportResource>();
  final isLoading = false.obs;
  final errorMessage = RxnString();

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      directory.value = await _searchResources(city: city.value, category: category.value);
      selected.value = null;
    } on Failure catch (failure) {
      errorMessage.value = failure is NetworkFailure
          ? 'Connecte-toi une première fois pour enregistrer l\'annuaire sur ton téléphone.'
          : failure.message;
    } finally {
      isLoading.value = false;
    }
  }

  void filterCity(String? value) {
    city.value = value;
    load();
  }

  void filterCategory(String? value) {
    category.value = value;
    load();
  }
}
