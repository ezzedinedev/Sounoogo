import '../../../core/error/failure_mapper.dart';
import '../../domain/entities/reference_data.dart';
import '../../domain/repositories/reference_repository.dart';
import '../datasources/reference_remote_data_source.dart';

class ReferenceRepositoryImpl implements ReferenceRepository {
  ReferenceRepositoryImpl(this._remote);

  final ReferenceRemoteDataSource _remote;

  @override
  Future<ReferenceData> load() => guardApi(_remote.fetch);
}
