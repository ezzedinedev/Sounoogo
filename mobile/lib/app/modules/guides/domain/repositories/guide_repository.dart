import '../entities/guide.dart';

abstract interface class GuideRepository {
  /// Hors ligne : la dernière liste enregistrée.
  Future<List<Guide>> all();

  /// Hors ligne : le guide s'il a déjà été ouvert une fois.
  Future<Guide> read(String slug);
}
