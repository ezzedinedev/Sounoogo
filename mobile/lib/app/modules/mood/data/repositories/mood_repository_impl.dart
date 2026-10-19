import '../../../../core/error/failure_mapper.dart';
import '../../domain/entities/mood_journal.dart';
import '../../domain/entities/mood_level.dart';
import '../../domain/repositories/mood_repository.dart';
import '../datasources/mood_remote_data_source.dart';

class MoodRepositoryImpl implements MoodRepository {
  MoodRepositoryImpl(this._remote);

  final MoodRemoteDataSource _remote;

  @override
  Future<MoodJournal> journal({int days = 30}) => guardApi(() => _remote.journal(days));

  @override
  Future<MoodJournal> record(MoodLevel level, {List<String> feelings = const [], String? note}) => guardApi(() async {
    await _remote.record(level, feelings, note);
    return _remote.journal(30);
  });
}
