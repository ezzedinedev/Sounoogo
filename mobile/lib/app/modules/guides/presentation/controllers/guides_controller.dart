import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../domain/entities/guide.dart';
import '../../domain/usecases/get_guides.dart';
import '../../domain/usecases/read_guide.dart';

class GuidesController extends GetxController {
  GuidesController({required this._getGuides, required this._readGuide});

  final GetGuides _getGuides;
  final ReadGuide _readGuide;

  final guides = <Guide>[].obs;
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
      guides.assignAll(await _getGuides());
    } on Failure catch (failure) {
      errorMessage.value = failure.message;
    } finally {
      isLoading.value = false;
    }
  }

  Future<Guide> read(String slug) => _readGuide(slug);
}
