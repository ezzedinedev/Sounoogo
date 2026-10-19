import '../../../../core/error/failure_mapper.dart';
import '../../domain/repositories/emergency_repository.dart';
import '../datasources/emergency_remote_data_source.dart';

class EmergencyRepositoryImpl implements EmergencyRepository {
  EmergencyRepositoryImpl(this._remote);

  final EmergencyRemoteDataSource _remote;

  @override
  Future<void> alertSupervisor() => guardApi(_remote.alert);
}
