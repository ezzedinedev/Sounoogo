import 'package:get/get.dart';

import '../../data/datasources/auth_remote_data_source.dart';
import '../../data/repositories/auth_repository_impl.dart';
import '../../domain/usecases/recover_account.dart';
import '../../domain/usecases/sign_in.dart';
import '../../domain/usecases/start_anonymous_session.dart';
import '../controllers/auth_controller.dart';

class AuthBinding extends Bindings {
  @override
  void dependencies() {
    if (Get.isRegistered<AuthController>()) {
      return;
    }
    final repository = AuthRepositoryImpl(AuthRemoteDataSource(Get.find()));
    Get.put(
      AuthController(
        startAnonymousSession: StartAnonymousSession(repository),
        recoverAccount: RecoverAccount(repository),
        signIn: SignIn(repository),
      ),
    );
  }
}
