import '../entities/guide.dart';
import '../repositories/guide_repository.dart';

final class GetGuides {
  const GetGuides(this._repository);

  final GuideRepository _repository;

  Future<List<Guide>> call() => _repository.all();
}
