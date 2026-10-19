import '../../../core/error/failure.dart';
import '../entities/reference_data.dart';
import '../repositories/reference_repository.dart';

/// Charge les numéros d'urgence, moyens de paiement et offres.
/// Ne lève jamais : sans réseau, les numéros publics par défaut restent disponibles.
final class LoadReferenceData {
  const LoadReferenceData(this._repository);

  final ReferenceRepository _repository;

  Future<ReferenceData> call() async {
    try {
      return await _repository.load();
    } on Failure {
      return const ReferenceData.fallback();
    }
  }
}
