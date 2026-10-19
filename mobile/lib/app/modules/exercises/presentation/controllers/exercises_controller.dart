import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../domain/entities/exercise.dart';
import '../../domain/entities/exercise_library.dart';
import '../../domain/usecases/download_exercise_audio.dart';
import '../../domain/usecases/get_exercises.dart';
import '../../domain/usecases/get_offline_audio.dart';

class ExercisesController extends GetxController {
  ExercisesController({required this._getExercises, required this._getOfflineAudio, required this._downloadExerciseAudio});

  final GetExercises _getExercises;
  final GetOfflineAudio _getOfflineAudio;
  final DownloadExerciseAudio _downloadExerciseAudio;

  final library = Rxn<ExerciseLibrary>();
  final isLoading = false.obs;
  final errorMessage = RxnString();
  final category = RxnString();

  List<Exercise> get visible => (library.value?.exercises ?? const <Exercise>[])
      .where((exercise) => category.value == null || exercise.category == category.value)
      .toList();

  /// Catégories présentes, dans l'ordre d'apparition : valeur → libellé.
  Map<String, String> get categories => {for (final e in library.value?.exercises ?? const <Exercise>[]) e.category: e.categoryLabel};

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      library.value = await _getExercises();
    } on Failure catch (failure) {
      errorMessage.value = failure is NetworkFailure
          ? 'Connecte-toi une première fois pour enregistrer les exercices sur ton téléphone.'
          : failure.message;
    } finally {
      isLoading.value = false;
    }
  }

  Exercise? find(String slug) => library.value?.find(slug);

  Future<String?> offlineAudio(Exercise exercise) => _getOfflineAudio(exercise);

  Future<String> downloadAudio(Exercise exercise, {void Function(double progress)? onProgress}) =>
      _downloadExerciseAudio(exercise, onProgress: onProgress);
}
