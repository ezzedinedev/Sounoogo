import 'package:get/get.dart';

import '../../data/datasources/mood_remote_data_source.dart';
import '../../data/repositories/mood_repository_impl.dart';
import '../../domain/repositories/mood_repository.dart';
import '../../domain/usecases/get_mood_journal.dart';
import '../../domain/usecases/record_mood.dart';
import '../controllers/mood_controller.dart';

/// Partagée par l'accueil (check-in) et l'écran du journal.
class MoodBinding extends Bindings {
  @override
  void dependencies() {
    Get
      ..lazyPut<MoodRepository>(() => MoodRepositoryImpl(MoodRemoteDataSource(Get.find())), fenix: true)
      ..lazyPut(() {
        final MoodRepository repository = Get.find();
        return MoodController(getMoodJournal: GetMoodJournal(repository), recordMood: RecordMood(repository));
      }, fenix: true);
  }
}
