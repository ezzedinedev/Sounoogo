import '../entities/support_resource.dart';
import '../repositories/directory_repository.dart';

final class SearchResources {
  const SearchResources(this._repository);

  final DirectoryRepository _repository;

  Future<ResourceDirectory> call({String? city, String? category}) => _repository.search(city: city, category: category);
}
