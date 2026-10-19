import '../entities/reference_data.dart';

abstract interface class ReferenceRepository {
  Future<ReferenceData> load();
}
