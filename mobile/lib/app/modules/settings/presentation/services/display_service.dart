import 'package:get/get.dart';

import '../../domain/entities/display_settings.dart';
import '../../domain/usecases/get_display_settings.dart';
import '../../domain/usecases/update_display_settings.dart';

/// Préférences d'affichage appliquées à toute l'app.
class DisplayService extends GetxService {
  DisplayService({required this._getDisplaySettings, required this._updateDisplaySettings});

  final GetDisplaySettings _getDisplaySettings;
  final UpdateDisplaySettings _updateDisplaySettings;

  final textSize = TextSize.normal.obs;

  @override
  void onInit() {
    super.onInit();
    _getDisplaySettings().then((settings) => textSize.value = settings.textSize);
  }

  Future<void> setTextSize(TextSize size) async {
    textSize.value = size;
    await _updateDisplaySettings(DisplaySettings(textSize: size));
  }
}
