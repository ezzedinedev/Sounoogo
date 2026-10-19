import 'package:get/get.dart';

import '../../data/datasources/safety_plan_local_data_source.dart';
import '../../data/datasources/safety_plan_remote_data_source.dart';
import '../../data/repositories/safety_plan_repository_impl.dart';
import '../../domain/repositories/safety_plan_repository.dart';
import '../../domain/usecases/delete_safety_plan.dart';
import '../../domain/usecases/get_safety_plan.dart';
import '../../domain/usecases/save_safety_plan.dart';
import '../controllers/safety_plan_controller.dart';

class SafetyPlanBinding extends Bindings {
  @override
  void dependencies() {
    Get
      ..lazyPut<SafetyPlanRepository>(
        () => SafetyPlanRepositoryImpl(SafetyPlanRemoteDataSource(Get.find()), SafetyPlanLocalDataSource(Get.find())),
        fenix: true,
      )
      ..lazyPut(() {
        final SafetyPlanRepository repository = Get.find();
        return SafetyPlanController(
          getSafetyPlan: GetSafetyPlan(repository),
          saveSafetyPlan: SaveSafetyPlan(repository),
          deleteSafetyPlan: DeleteSafetyPlan(repository),
        );
      }, fenix: true);
  }
}
