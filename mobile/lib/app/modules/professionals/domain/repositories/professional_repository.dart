import '../entities/professional.dart';

abstract interface class ProfessionalRepository {
  Future<List<Professional>> getAll({String? specialty, String? mode});

  Future<Professional> getById(int id);
}
