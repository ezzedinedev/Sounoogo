import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../domain/entities/mood_journal.dart';
import '../../domain/entities/mood_level.dart';
import '../../domain/usecases/get_mood_journal.dart';
import '../../domain/usecases/record_mood.dart';
import '../../../../core/widgets/app_toast.dart';

class MoodController extends GetxController {
  MoodController({required this._getMoodJournal, required this._recordMood});

  final GetMoodJournal _getMoodJournal;
  final RecordMood _recordMood;

  final journal = Rxn<MoodJournal>();
  final isLoading = false.obs;
  final isSaving = false.obs;
  final errorMessage = RxnString();

  @override
  void onInit() {
    super.onInit();
    load();
  }

  Future<void> load() async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      journal.value = await _getMoodJournal();
    } on Failure catch (failure) {
      errorMessage.value = failure is NetworkFailure ? 'Impossible de charger ton journal.' : failure.message;
    } finally {
      isLoading.value = false;
    }
  }

  /// Renvoie `true` si le check-in est enregistré.
  Future<bool> checkIn(MoodLevel level, {List<String> feelings = const [], String? note}) async {
    if (isSaving.value) {
      return false;
    }
    isSaving.value = true;
    try {
      journal.value = await _recordMood(level, feelings: feelings, note: note);
      return true;
    } on Failure catch (failure) {
      AppToast.show('Check-in non enregistré', failure.message);
      return false;
    } finally {
      isSaving.value = false;
    }
  }
}
