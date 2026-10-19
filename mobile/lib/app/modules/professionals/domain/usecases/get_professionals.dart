import '../entities/professional.dart';
import '../repositories/professional_repository.dart';

final class GetProfessionals {
  const GetProfessionals(this._repository);

  final ProfessionalRepository _repository;

  Future<List<Professional>> call({String? specialty, String? mode}) => _repository.getAll(specialty: specialty, mode: mode);
}
