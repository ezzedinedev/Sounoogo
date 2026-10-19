import '../entities/guide.dart';
import '../repositories/guide_repository.dart';

final class ReadGuide {
  const ReadGuide(this._repository);

  final GuideRepository _repository;

  Future<Guide> call(String slug) => _repository.read(slug);
}
