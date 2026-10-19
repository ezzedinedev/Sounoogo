import '../../../../core/error/failure.dart';
import '../../../../core/error/failure_mapper.dart';
import '../../../../core/storage/json_cache.dart';
import '../../domain/entities/support_resource.dart';
import '../../domain/repositories/directory_repository.dart';
import '../datasources/directory_remote_data_source.dart';
import '../models/support_resource_model.dart';

class DirectoryRepositoryImpl implements DirectoryRepository {
  DirectoryRepositoryImpl(this._remote, this._cache);

  final DirectoryRemoteDataSource _remote;
  final JsonCache _cache;

  @override
  Future<ResourceDirectory> search({String? city, String? category}) async {
    final key = 'resources.${city ?? '*'}.${category ?? '*'}';
    try {
      final json = await guardApi(() => _remote.search(city: city, category: category));
      await _cache.write(key, json);
      return ResourceDirectoryModel.fromJson(json);
    } on NetworkFailure {
      final cached = await _cache.read(key);
      if (cached is! Map<String, dynamic>) {
        rethrow;
      }
      return ResourceDirectoryModel.fromJson(cached, fromCache: true);
    }
  }
}
