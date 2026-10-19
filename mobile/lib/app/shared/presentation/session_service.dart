import 'package:get/get.dart';

import '../domain/entities/emergency_contact.dart';
import '../domain/entities/payment_provider.dart';
import '../domain/entities/plan.dart';
import '../domain/entities/user.dart';
import '../domain/usecases/end_session.dart';
import '../domain/usecases/get_current_user.dart';
import '../domain/usecases/load_reference_data.dart';
import '../domain/usecases/restore_session.dart';
import '../domain/usecases/save_session.dart';

/// État global de la session, observé par les écrans : membre connecté et données de référence.
class SessionService extends GetxService {
  SessionService({
    required this._restoreSession,
    required this._getCurrentUser,
    required this._saveSession,
    required this._endSession,
    required this._loadReferenceData,
  });

  final RestoreSession _restoreSession;
  final GetCurrentUser _getCurrentUser;
  final SaveSession _saveSession;
  final EndSession _endSession;
  final LoadReferenceData _loadReferenceData;

  final user = Rxn<User>();
  final emergencyContacts = <EmergencyContact>[].obs;
  final paymentProviders = <PaymentProvider>[].obs;
  final plans = <Plan>[].obs;

  bool get isSignedIn => user.value != null;

  /// Au démarrage : charge les données de référence et restaure la session si possible.
  Future<void> restore() async {
    await loadReferenceData();
    user.value = await _restoreSession();
  }

  Future<void> loadReferenceData() async {
    final data = await _loadReferenceData();
    if (data.isFallback && emergencyContacts.isNotEmpty) {
      return;
    }
    emergencyContacts.assignAll(data.emergencyContacts);
    paymentProviders.assignAll(data.paymentProviders);
    plans.assignAll(data.plans);
  }

  Future<void> refreshUser() async => user.value = await _getCurrentUser();

  /// Ouvre la session après une authentification réussie.
  Future<void> open(String token, User profile) async {
    await _saveSession(token);
    user.value = profile;
  }

  void updateUser(User profile) => user.value = profile;

  Future<void> signOut({bool remote = true}) async {
    await _endSession(remote: remote);
    user.value = null;
  }

  /// Appelé par le client HTTP quand le serveur refuse le jeton.
  Future<void> expire() => signOut(remote: false);
}
