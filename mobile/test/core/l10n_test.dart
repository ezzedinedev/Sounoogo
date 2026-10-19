import 'dart:convert';
import 'dart:io';

import 'package:flutter_test/flutter_test.dart';

/// Chaque langue ajoutée (mooré, dioula…) doit traduire toutes les clés du fichier de référence.
void main() {
  test('les fichiers de traduction ont les mêmes clés que le français', () {
    Set<String> keysOf(File file) =>
        (jsonDecode(file.readAsStringSync()) as Map<String, dynamic>).keys.where((key) => !key.startsWith('@')).toSet();

    final reference = keysOf(File('lib/l10n/app_fr.arb'));
    final others = Directory('lib/l10n')
        .listSync()
        .whereType<File>()
        .where((f) => f.path.endsWith('.arb') && !f.path.endsWith('app_fr.arb'));

    expect(reference, isNotEmpty);
    for (final file in others) {
      expect(keysOf(file), reference, reason: '${file.path} doit traduire exactement les clés de app_fr.arb');
    }
  });
}
