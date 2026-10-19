import 'dart:convert';

import '../../../../core/storage/secure_cache.dart';
import 'pin_hasher.dart';

/// État du verrou stocké sur l'appareil, chiffré. Effacé à la déconnexion.
class PinRecord {
  const PinRecord({required this.salt, required this.hash, required this.length, this.failedAttempts = 0, this.lockedUntil});

  final List<int> salt;
  final int length;
  final List<int> hash;
  final int failedAttempts;
  final DateTime? lockedUntil;
}

class PinLocalDataSource {
  PinLocalDataSource(this._cache, {this.hasher = const PinHasher()});

  static const _key = 'app_lock';

  final SecureCache _cache;
  final PinHasher hasher;

  Future<PinRecord?> read() async {
    final json = await _cache.read(_key);
    if (json == null) {
      return null;
    }
    return PinRecord(
      salt: base64Decode(json['salt'] as String),
      hash: base64Decode(json['hash'] as String),
      length: json['length'] as int,
      failedAttempts: json['failed_attempts'] as int? ?? 0,
      lockedUntil: DateTime.tryParse(json['locked_until'] as String? ?? ''),
    );
  }

  Future<void> write(PinRecord record) => _cache.write(_key, {
    'salt': base64Encode(record.salt),
    'hash': base64Encode(record.hash),
    'length': record.length,
    'failed_attempts': record.failedAttempts,
    if (record.lockedUntil != null) 'locked_until': record.lockedUntil!.toIso8601String(),
  });

  Future<void> clear() => _cache.delete(_key);
}
