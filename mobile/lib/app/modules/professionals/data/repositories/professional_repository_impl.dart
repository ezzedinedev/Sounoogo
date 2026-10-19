import '../../../../core/error/failure_mapper.dart';
import '../../domain/entities/professional.dart';
import '../../domain/repositories/professional_repository.dart';
import '../datasources/professional_remote_data_source.dart';

class ProfessionalRepositoryImpl implements ProfessionalRepository {
  ProfessionalRepositoryImpl(this._remote);

  final ProfessionalRemoteDataSource _remote;

  @override
  Future<List<Professional>> getAll({String? specialty, String? mode}) => guardApi(() => _remote.getAll(specialty: specialty, mode: mode));

  @override
  Future<Professional> getById(int id) => guardApi(() => _remote.getById(id));
}
