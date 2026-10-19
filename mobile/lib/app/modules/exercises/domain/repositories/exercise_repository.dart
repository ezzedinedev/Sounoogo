import '../entities/exercise.dart';
import '../entities/exercise_library.dart';

abstract interface class ExerciseRepository {
  /// Depuis l'API si possible, sinon la copie hors ligne.
  Future<ExerciseLibrary> library();

  /// Chemin du fichier audio déjà téléchargé, ou `null`.
  Future<String?> offlineAudio(Exercise exercise);

  /// Télécharge l'audio pour l'écouter sans réseau et renvoie son chemin.
  Future<String> downloadAudio(Exercise exercise, {void Function(double progress)? onProgress});
}
