import '../entities/support_resource.dart';

abstract interface class DirectoryRepository {
  Future<ResourceDirectory> search({String? city, String? category});
}
