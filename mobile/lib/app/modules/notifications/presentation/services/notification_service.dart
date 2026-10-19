import 'dart:async';

import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../shared/presentation/session_service.dart';
import '../../domain/entities/push_event.dart';
import '../../domain/entities/reminder_settings.dart';
import '../../domain/usecases/manage_push_device.dart';
import '../../domain/usecases/manage_reminder.dart';

/// Rappels et notifications push : inscription de l'appareil quand le membre est connecté,
/// désinscription avant la déconnexion, et ouverture du bon écran quand une notification est touchée.
class NotificationService extends GetxService {
  NotificationService({required this._manageReminder, required this._managePushDevice});

  final ManageReminder _manageReminder;
  final ManagePushDevice _managePushDevice;
  final SessionService _session = Get.find();

  final reminder = const ReminderSettings().obs;

  /// Notification touchée avant que l'accueil soit prêt (app lancée par la notification).
  PushTarget? _pending;
  StreamSubscription<PushTarget>? _opened;

  @override
  void onInit() {
    super.onInit();
    _start();
  }

  Future<void> _start() async {
    await _managePushDevice.start();
    reminder.value = await _manageReminder.current();
    _opened = _managePushDevice.opened().listen(_open);
    _session.onBeforeSignOut(_managePushDevice.unregister);
    ever(_session.user, (user) {
      if (user != null) {
        _managePushDevice.register().ignore();
      }
    });
    if (_session.isSignedIn) {
      _managePushDevice.register().ignore();
    }
  }

  /// Renvoie false si le membre a refusé les notifications.
  Future<bool> updateReminder(ReminderSettings settings) async {
    final saved = await _manageReminder.save(settings);
    if (saved) {
      reminder.value = settings;
    }
    return saved;
  }

  void _open(PushTarget target) {
    if (!_session.isSignedIn || Get.currentRoute == AppRoutes.splash) {
      _pending = target;
      return;
    }
    switch (target) {
      case ConversationTarget():
        Get.toNamed(AppRoutes.conversation);
      case BookingTarget():
        Get.toNamed(AppRoutes.bookings);
      case CheckInTarget():
        Get.toNamed(AppRoutes.mood);
    }
  }

  /// Appelé une fois l'accueil affiché.
  void openPending() {
    final target = _pending;
    _pending = null;
    if (target != null) {
      _open(target);
    }
  }

  @override
  void onClose() {
    _opened?.cancel();
    super.onClose();
  }
}
