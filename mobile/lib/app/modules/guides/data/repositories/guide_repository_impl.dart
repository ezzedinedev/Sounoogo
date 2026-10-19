import '../../../../core/error/failure.dart';
import '../../../../core/error/failure_mapper.dart';
import '../../../../core/storage/json_cache.dart';
import '../../domain/entities/guide.dart';
import '../../domain/repositories/guide_repository.dart';
import '../datasources/guide_remote_data_source.dart';
import '../models/guide_model.dart';

class GuideRepositoryImpl implements GuideRepository {
  GuideRepositoryImpl(this._remote, this._cache);

  final GuideRemoteDataSource _remote;
  final JsonCache _cache;

  @override
  Future<List<Guide>> all() async {
    final json = await _withCache('guides', () => guardApi(_remote.list));
    return (json as List).cast<Map<String, dynamic>>().map(GuideModel.fromJson).toList();
  }

  @override
  Future<Guide> read(String slug) async =>
      GuideModel.fromJson(await _withCache('guide.$slug', () => guardApi(() => _remote.find(slug))) as Map<String, dynamic>);

  /// Réseau d'abord ; sans réseau, la dernière copie enregistrée.
  Future<Object> _withCache(String key, Future<Object> Function() fetch) async {
    try {
      final json = await fetch();
      await _cache.write(key, json);
      return json;
    } on NetworkFailure {
      return await _cache.read(key) ?? (throw const NetworkFailure('Ce contenu n\'est pas encore enregistré sur ton téléphone.'));
    }
  }
}
