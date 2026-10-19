import 'package:get/get.dart';

import '../../data/datasources/directory_remote_data_source.dart';
import '../../data/repositories/directory_repository_impl.dart';
import '../../domain/repositories/directory_repository.dart';
import '../../domain/usecases/search_resources.dart';
import '../controllers/directory_controller.dart';

class DirectoryBinding extends Bindings {
  @override
  void dependencies() {
    Get
      ..lazyPut<DirectoryRepository>(() => DirectoryRepositoryImpl(DirectoryRemoteDataSource(Get.find()), Get.find()), fenix: true)
      ..lazyPut(() => DirectoryController(SearchResources(Get.find())));
  }
}
