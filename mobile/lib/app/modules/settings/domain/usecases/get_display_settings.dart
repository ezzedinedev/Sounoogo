import '../entities/display_settings.dart';
import '../repositories/settings_repository.dart';

final class GetDisplaySettings {
  const GetDisplaySettings(this._repository);

  final SettingsRepository _repository;

  Future<DisplaySettings> call() => _repository.display();
}
