import 'exercise.dart';

/// Exercices disponibles, et leur provenance.
class ExerciseLibrary {
  const ExerciseLibrary({required this.exercises, this.fromCache = false});

  final List<Exercise> exercises;

  /// Vrai si le réseau manque : la dernière copie enregistrée sur le téléphone est utilisée.
  final bool fromCache;

  Exercise? find(String slug) => exercises.where((e) => e.slug == slug).firstOrNull;
}
