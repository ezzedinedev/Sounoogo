import 'dart:async';

import 'package:shared_preferences/shared_preferences.dart';

import '../../../../core/error/failure_mapper.dart';
import '../../domain/entities/push_event.dart';
import '../../domain/entities/reminder_settings.dart';
import '../../domain/repositories/notification_repository.dart';
import '../datasources/device_remote_data_source.dart';
import '../datasources/local_notifications_data_source.dart';
import '../datasources/push_data_source.dart';

class NotificationRepositoryImpl implements NotificationRepository {
  NotificationRepositoryImpl(this._local, this._push, this._devices, {SharedPreferencesAsync? preferences})
    : _preferences = preferences ?? SharedPreferencesAsync();

  static const _reminderKey = 'sunoogo.reminder';

  final LocalNotificationsDataSource _local;
  final PushDataSource _push;
  final DeviceRemoteDataSource _devices;
  final SharedPreferencesAsync _preferences;

  @override
  Future<void> start() => _local.initialize();

  @override
  Future<ReminderSettings> reminder() async {
    final saved = await _preferences.getStringList(_reminderKey);
    if (saved == null || saved.length != 3) {
      return const ReminderSettings();
    }
    return ReminderSettings(enabled: saved[0] == '1', hour: int.parse(saved[1]), minute: int.parse(saved[2]));
  }

  @override
  Future<bool> saveReminder(ReminderSettings settings) async {
    if (settings.enabled && !await _local.requestPermission()) {
      return false;
    }
    await _preferences.setStringList(_reminderKey, [if (settings.enabled) '1' else '0', '${settings.hour}', '${settings.minute}']);
    await _local.cancel(LocalNotificationsDataSource.dailyReminderId);
    if (settings.enabled) {
      // Texte neutre : rien ne dit sur l'écran verrouillé de quoi il s'agit.
      await _local.scheduleDaily(
        hour: settings.hour,
        minute: settings.minute,
        title: 'Un moment pour toi',
        body: 'Comment s\'est passée ta journée ?',
        data: {'type': 'checkin'},
      );
    }
    return true;
  }

  @override
  Future<void> registerDevice() async {
    final token = await _push.token();
    if (token != null) {
      await guardApi(() => _devices.register(token));
    }
  }

  @override
  Future<void> unregisterDevice() async {
    final token = await _push.currentToken();
    if (token != null) {
      await guardApi(() => _devices.unregister(token));
      await _push.deleteToken();
    }
  }

  @override
  Stream<PushTarget> openedTargets() {
    final controller = StreamController<PushTarget>.broadcast();
    void forward(Map<String, dynamic> data) {
      final target = PushTarget.fromData(data);
      if (target != null) {
        controller.add(target);
      }
    }

    _local.taps.listen(forward);
    _push.opened().listen(forward);
    // Messages reçus app ouverte : on les affiche nous-mêmes, avec le même texte neutre.
    _push.foreground().listen((message) {
      final notification = message.notification;
      if (notification != null) {
        _local.show(title: notification.title ?? 'SUNOOGO', body: notification.body ?? '', data: message.data);
      }
    });
    return controller.stream;
  }
}
