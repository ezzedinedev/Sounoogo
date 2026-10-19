import '../entities/privacy_settings.dart';
import '../repositories/settings_repository.dart';

final class GetPrivacySettings {
  const GetPrivacySettings(this._repository);

  final SettingsRepository _repository;

  Future<PrivacySettings> call() => _repository.privacy();
}
