import 'package:get/get.dart';

import '../../data/datasources/profile_remote_data_source.dart';
import '../../data/repositories/profile_repository_impl.dart';
import '../../domain/repositories/profile_repository.dart';
import '../../domain/usecases/delete_account.dart';
import '../../domain/usecases/secure_account.dart';
import '../controllers/profile_controller.dart';

class ProfileBinding extends Bindings {
  @override
  void dependencies() {
    Get
      ..lazyPut<ProfileRepository>(() => ProfileRepositoryImpl(ProfileRemoteDataSource(Get.find())), fenix: true)
      ..lazyPut(() {
        final ProfileRepository repository = Get.find();
        return ProfileController(secureAccount: SecureAccount(repository), deleteAccount: DeleteAccount(repository));
      }, fenix: true);
  }
}
