import 'package:get/get.dart';

import '../../data/datasources/emergency_remote_data_source.dart';
import '../../data/repositories/emergency_repository_impl.dart';
import '../../domain/repositories/emergency_repository.dart';
import '../../domain/usecases/alert_supervisor.dart';
import '../controllers/emergency_controller.dart';

/// Toujours disponible : le panneau d'urgence s'ouvre depuis n'importe quel écran.
class EmergencyBinding extends Bindings {
  @override
  void dependencies() {
    Get
      ..lazyPut<EmergencyRepository>(() => EmergencyRepositoryImpl(EmergencyRemoteDataSource(Get.find())), fenix: true)
      ..lazyPut(() => AlertSupervisor(Get.find()), fenix: true)
      ..lazyPut(() => EmergencyController(Get.find()), fenix: true);
  }
}
