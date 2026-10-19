import '../../../../core/error/failure.dart';
import '../entities/certificate.dart';
import '../repositories/course_repository.dart';

/// Demande l'attestation avec le nom à y inscrire (le compte, lui, peut rester anonyme).
final class IssueCertificate {
  const IssueCertificate(this._repository);

  final CourseRepository _repository;

  static final _allowed = RegExp(r"^[\p{L}\p{M}\s'’.-]+$", unicode: true);

  Future<Certificate> call(String slug, String holderName) {
    final name = holderName.trim().replaceAll(RegExp(r'\s+'), ' ');
    if (name.length < 2 || name.length > 80 || !_allowed.hasMatch(name)) {
      throw const ValidationFailure('Indique un nom de 2 à 80 lettres (espaces, tirets et apostrophes acceptés).');
    }
    return _repository.issueCertificate(slug, name);
  }
}
