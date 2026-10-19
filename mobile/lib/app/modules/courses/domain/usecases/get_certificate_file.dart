import '../entities/certificate.dart';
import '../repositories/course_repository.dart';

final class GetCertificateFile {
  const GetCertificateFile(this._repository);

  final CourseRepository _repository;

  Future<String> call(Certificate certificate) => _repository.certificateFile(certificate);
}
