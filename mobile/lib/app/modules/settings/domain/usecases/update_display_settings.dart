import '../entities/display_settings.dart';
import '../repositories/settings_repository.dart';

final class UpdateDisplaySettings {
  const UpdateDisplaySettings(this._repository);

  final SettingsRepository _repository;

  Future<void> call(DisplaySettings settings) => _repository.saveDisplay(settings);
}
