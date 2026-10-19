import 'dart:convert';

import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// Copie locale chiffrée de données sensibles qui doivent rester accessibles hors ligne
/// (le plan de sécurité, par exemple). Vidée à la déconnexion.
class SecureCache {
  SecureCache({FlutterSecureStorage? storage}) : _storage = storage ?? const FlutterSecureStorage();

  static const _prefix = 'sunoogo.cache.';

  final FlutterSecureStorage _storage;

  Future<Map<String, dynamic>?> read(String key) async {
    final raw = await _storage.read(key: '$_prefix$key');
    return raw == null ? null : jsonDecode(raw) as Map<String, dynamic>;
  }

  Future<void> write(String key, Map<String, dynamic> value) => _storage.write(key: '$_prefix$key', value: jsonEncode(value));

  Future<void> delete(String key) => _storage.delete(key: '$_prefix$key');

  Future<void> clear() async {
    final all = await _storage.readAll();
    for (final key in all.keys.where((key) => key.startsWith(_prefix))) {
      await _storage.delete(key: key);
    }
  }
}
