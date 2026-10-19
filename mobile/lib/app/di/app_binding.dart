import 'package:get/get.dart';

import '../core/network/api_provider.dart';
import '../core/realtime/realtime_service.dart';
import '../core/storage/secure_cache.dart';
import '../core/storage/token_storage.dart';
import '../modules/emergency/presentation/bindings/emergency_binding.dart';
import '../modules/payments/presentation/bindings/payment_binding.dart';
import '../shared/data/datasources/reference_remote_data_source.dart';
import '../shared/data/datasources/session_remote_data_source.dart';
import '../shared/data/repositories/reference_repository_impl.dart';
import '../shared/data/repositories/session_repository_impl.dart';
import '../shared/domain/usecases/end_session.dart';
import '../shared/domain/usecases/get_current_user.dart';
import '../shared/domain/usecases/load_reference_data.dart';
import '../shared/domain/usecases/restore_session.dart';
import '../shared/domain/usecases/save_session.dart';
import '../shared/presentation/session_service.dart';

/// Racine de composition : dépendances transverses, vivantes pendant toute la vie de l'app.
/// Chaque module enregistre les siennes dans sa propre binding.
class AppBinding extends Bindings {
  @override
  void dependencies() {
    final tokens = TokenStorage();
    final cache = SecureCache();
    final api = ApiProvider(tokenReader: tokens.read, onUnauthorized: () => Get.find<SessionService>().expire());
    final sessions = SessionRepositoryImpl(SessionRemoteDataSource(api), tokens, cache);

    Get
      ..put(tokens, permanent: true)
      ..put(cache, permanent: true)
      ..put(api, permanent: true)
      ..put(RealtimeService(tokens), permanent: true)
      ..put(
        SessionService(
          restoreSession: RestoreSession(sessions),
          getCurrentUser: GetCurrentUser(sessions),
          saveSession: SaveSession(sessions),
          endSession: EndSession(sessions),
          loadReferenceData: LoadReferenceData(ReferenceRepositoryImpl(ReferenceRemoteDataSource(api))),
        ),
        permanent: true,
      );

    EmergencyBinding().dependencies();
    PaymentBinding().dependencies();
  }
}
