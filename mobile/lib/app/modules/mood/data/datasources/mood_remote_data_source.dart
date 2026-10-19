import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';
import '../../domain/entities/mood_journal.dart';
import '../../domain/entities/mood_level.dart';
import '../models/mood_journal_model.dart';

class MoodRemoteDataSource {
  MoodRemoteDataSource(this._api);

  final ApiProvider _api;

  Future<MoodJournal> journal(int days) async => MoodJournalModel.fromJson(await _api.get(ApiConstants.moods, query: {'days': days}));

  Future<void> record(MoodLevel level, List<String> feelings, String? note) =>
      _api.post(ApiConstants.moods, data: {'mood': level.value, 'feelings': feelings, 'note': ?note});
}
