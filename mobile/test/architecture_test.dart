import 'dart:io';

import 'package:flutter_test/flutter_test.dart';

/// Règles de dépendance de la clean architecture, vérifiées sur les imports :
/// domaine ← données ← présentation. Le domaine reste du Dart pur.
void main() {
  final sources = Directory('lib/app')
      .listSync(recursive: true)
      .whereType<File>()
      .where((file) => file.path.endsWith('.dart'))
      .map((file) => (path: file.path.replaceAll(r'\', '/'), imports: _imports(file)))
      .toList();

  Iterable<({String path, List<String> imports})> layer(String name) => sources.where((s) => s.path.contains('/$name/'));

  void expectNoImport(Iterable<({String path, List<String> imports})> files, bool Function(String) forbidden, String rule) {
    final violations = [
      for (final file in files)
        for (final import in file.imports.where(forbidden)) '${file.path} → $import',
    ];
    expect(violations, isEmpty, reason: rule);
  }

  test('le domaine ne dépend ni des frameworks ni des autres couches', () {
    expectNoImport(
      layer('domain'),
      (i) => i.startsWith('package:') || i.contains('/data/') || i.contains('/presentation/') || i.contains('/network/'),
      'Le domaine doit rester du Dart pur.',
    );
  });

  test('la couche données ne dépend pas de la présentation', () {
    expectNoImport(
      layer('data'),
      (i) => i.contains('/presentation/') || i.startsWith('package:get/') || i.startsWith('package:flutter/'),
      'Les data sources et repositories ignorent les écrans et GetX.',
    );
  });

  test('seules les bindings connaissent la couche données', () {
    expectNoImport(
      layer('presentation').where((s) => !s.path.contains('/bindings/')),
      (i) => i.contains('/data/') || i.contains('core/network/'),
      'Contrôleurs et écrans passent par les cas d\'usage.',
    );
  });

  test('chaque repository du domaine est un contrat abstrait', () {
    for (final file in layer('domain').where((s) => s.path.contains('/repositories/'))) {
      expect(File(file.path).readAsStringSync(), contains('abstract interface class'), reason: file.path);
    }
  });
}

List<String> _imports(File file) => RegExp(r"^import '([^']+)';", multiLine: true)
    .allMatches(file.readAsStringSync())
    .map((m) => m.group(1)!)
    .toList();
