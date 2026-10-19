import 'package:get/get.dart';

import '../../data/datasources/guide_remote_data_source.dart';
import '../../data/repositories/guide_repository_impl.dart';
import '../../domain/repositories/guide_repository.dart';
import '../../domain/usecases/get_guides.dart';
import '../../domain/usecases/read_guide.dart';
import '../controllers/guides_controller.dart';

class GuidesBinding extends Bindings {
  @override
  void dependencies() {
    Get
      ..lazyPut<GuideRepository>(() => GuideRepositoryImpl(GuideRemoteDataSource(Get.find()), Get.find()), fenix: true)
      ..lazyPut(() {
        final GuideRepository repository = Get.find();
        return GuidesController(getGuides: GetGuides(repository), readGuide: ReadGuide(repository));
      }, fenix: true);
  }
}
