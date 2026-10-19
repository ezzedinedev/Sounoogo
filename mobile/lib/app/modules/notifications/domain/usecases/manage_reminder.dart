import '../entities/reminder_settings.dart';
import '../repositories/notification_repository.dart';

final class ManageReminder {
  const ManageReminder(this._repository);

  final NotificationRepository _repository;

  Future<ReminderSettings> current() => _repository.reminder();

  Future<bool> save(ReminderSettings settings) => _repository.saveReminder(settings);
}
