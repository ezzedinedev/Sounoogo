import 'dart:convert';

import 'package:shared_preferences/shared_preferences.dart';

/// Copie locale de contenus publics (exercices, guides, annuaire) pour un usage hors ligne.
/// Rien de personnel ici : les données sensibles passent par [SecureCache].
class JsonCache {
  JsonCache({SharedPreferencesAsync? preferences}) : _preferences = preferences ?? SharedPreferencesAsync();

  static const _prefix = 'sunoogo.content.';

  final SharedPreferencesAsync _preferences;

  Future<Object?> read(String key) async {
    final raw = await _preferences.getString('$_prefix$key');
    return raw == null ? null : jsonDecode(raw);
  }

  Future<void> write(String key, Object value) => _preferences.setString('$_prefix$key', jsonEncode(value));
}
