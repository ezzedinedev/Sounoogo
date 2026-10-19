import 'package:get/get.dart';

import '../../data/datasources/device_remote_data_source.dart';
import '../../data/datasources/local_notifications_data_source.dart';
import '../../data/datasources/push_data_source.dart';
import '../../data/repositories/notification_repository_impl.dart';
import '../../domain/usecases/manage_push_device.dart';
import '../../domain/usecases/manage_reminder.dart';
import '../services/notification_service.dart';

/// Global : doit être prêt dès le démarrage pour recevoir la notification qui a lancé l'app.
class NotificationBinding extends Bindings {
  @override
  void dependencies() {
    final repository = NotificationRepositoryImpl(LocalNotificationsDataSource(), PushDataSource(), DeviceRemoteDataSource(Get.find()));
    Get.put(
      NotificationService(manageReminder: ManageReminder(repository), managePushDevice: ManagePushDevice(repository)),
      permanent: true,
    );
  }
}
