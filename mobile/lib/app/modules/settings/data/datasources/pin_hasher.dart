import 'dart:convert';
import 'dart:math';

import 'package:crypto/crypto.dart';
import 'package:flutter/foundation.dart';

/// PBKDF2-HMAC-SHA256 : le code n'est jamais stocké, seulement son empreinte salée.
class PinHasher {
  const PinHasher({this.iterations = 60000});

  final int iterations;

  List<int> newSalt() {
    final random = Random.secure();
    return List<int>.generate(16, (_) => random.nextInt(256));
  }

  /// Calcul en tâche de fond pour ne pas figer l'interface.
  Future<List<int>> hash(String pin, List<int> salt) {
    final rounds = iterations;
    return compute(_deriveMessage, (pin, salt, rounds));
  }

  static List<int> _deriveMessage((String, List<int>, int) message) => _derive(message.$1, message.$2, message.$3);

  static List<int> _derive(String pin, List<int> salt, int iterations) {
    final hmac = Hmac(sha256, utf8.encode(pin));
    var block = hmac.convert([...salt, 0, 0, 0, 1]).bytes;
    final result = List<int>.of(block);
    for (var i = 1; i < iterations; i++) {
      block = hmac.convert(block).bytes;
      for (var j = 0; j < result.length; j++) {
        result[j] ^= block[j];
      }
    }
    return result;
  }

  /// Comparaison en temps constant.
  Future<bool> matches(String pin, List<int> salt, List<int> expected) async {
    final actual = await hash(pin, salt);
    if (actual.length != expected.length) {
      return false;
    }
    var difference = 0;
    for (var i = 0; i < actual.length; i++) {
      difference |= actual[i] ^ expected[i];
    }
    return difference == 0;
  }
}
