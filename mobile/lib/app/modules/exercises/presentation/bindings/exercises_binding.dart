import 'package:get/get.dart';

import '../../data/datasources/exercise_audio_data_source.dart';
import '../../data/datasources/exercise_remote_data_source.dart';
import '../../data/repositories/exercise_repository_impl.dart';
import '../../domain/repositories/exercise_repository.dart';
import '../../domain/usecases/download_exercise_audio.dart';
import '../../domain/usecases/get_exercises.dart';
import '../../domain/usecases/get_offline_audio.dart';
import '../controllers/exercises_controller.dart';

class ExercisesBinding extends Bindings {
  @override
  void dependencies() {
    Get
      ..lazyPut<ExerciseRepository>(
        () => ExerciseRepositoryImpl(ExerciseRemoteDataSource(Get.find()), Get.find(), ExerciseAudioDataSource()),
        fenix: true,
      )
      ..lazyPut(() {
        final ExerciseRepository repository = Get.find();
        return ExercisesController(
          getExercises: GetExercises(repository),
          getOfflineAudio: GetOfflineAudio(repository),
          downloadExerciseAudio: DownloadExerciseAudio(repository),
        );
      }, fenix: true);
  }
}
