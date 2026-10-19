import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get/get.dart';

import '../constants/api_constants.dart';
import '../models/emergency_contact_model.dart';
import '../models/payment_model.dart';
import '../models/plan_model.dart';
import '../models/user_model.dart';
import '../network/api_provider.dart';

/// Session du membre : jeton (stockage chiffré du téléphone), profil et données de référence.
class SessionService extends GetxService {
  SessionService({FlutterSecureStorage? storage}) : _storage = storage ?? const FlutterSecureStorage();

  static const _tokenKey = 'sunoogo.token';

  final FlutterSecureStorage _storage;
  late final ApiProvider api = ApiProvider(tokenReader: token, onUnauthorized: _expire);

  final user = Rxn<UserModel>();
  final emergencyContacts = <EmergencyContactModel>[].obs;
  final paymentProviders = <PaymentProviderModel>[].obs;
  final plans = <PlanModel>[].obs;

  String? _token;
  bool _tokenLoaded = false;

  bool get isSignedIn => user.value != null;

  Future<String?> token() async {
    if (!_tokenLoaded) {
      _token = await _storage.read(key: _tokenKey);
      _tokenLoaded = true;
    }
    return _token;
  }

  /// Au démarrage : restaure la session si un jeton existe, et charge les numéros d'urgence.
  Future<void> restore() async {
    await loadMeta();
    if (await token() == null) {
      return;
    }
    try {
      await refreshUser();
    } catch (_) {
      // Jeton expiré ou réseau absent : le membre reprendra depuis l'accueil.
    }
  }

  Future<void> loadMeta() async {
    try {
      final meta = await api.get(ApiConstants.meta);
      emergencyContacts.assignAll((meta['emergency_contacts'] as List).map((e) => EmergencyContactModel.fromJson(e as Map<String, dynamic>)));
      paymentProviders.assignAll((meta['payment_providers'] as List).map((e) => PaymentProviderModel.fromJson(e as Map<String, dynamic>)));
      plans.assignAll((meta['plans'] as List).map((e) => PlanModel.fromJson(e as Map<String, dynamic>)));
    } catch (_) {
      // Hors ligne : on garde les numéros publics par défaut pour que l'urgence reste accessible.
      if (emergencyContacts.isEmpty) {
        emergencyContacts.assignAll(const [
          EmergencyContactModel(label: 'Police secours', phone: '17'),
          EmergencyContactModel(label: 'Sapeurs-pompiers', phone: '18'),
        ]);
      }
    }
  }

  Future<void> refreshUser() async {
    final response = await api.get(ApiConstants.me);
    user.value = UserModel.fromJson(response['data'] as Map<String, dynamic>);
  }

  Future<void> signIn(String token, Map<String, dynamic> userJson) async {
    _token = token;
    _tokenLoaded = true;
    await _storage.write(key: _tokenKey, value: token);
    user.value = UserModel.fromJson(userJson);
  }

  Future<void> signOut({bool remote = true}) async {
    if (remote) {
      try {
        await api.post(ApiConstants.authLogout);
      } catch (_) {}
    }
    await _clear();
  }

  Future<void> _expire() => _clear();

  Future<void> _clear() async {
    _token = null;
    _tokenLoaded = true;
    await _storage.delete(key: _tokenKey);
    user.value = null;
  }
}
