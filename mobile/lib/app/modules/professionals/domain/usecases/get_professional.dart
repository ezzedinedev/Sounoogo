import '../entities/professional.dart';
import '../repositories/professional_repository.dart';

final class GetProfessional {
  const GetProfessional(this._repository);

  final ProfessionalRepository _repository;

  Future<Professional> call(int id) => _repository.getById(id);
}
