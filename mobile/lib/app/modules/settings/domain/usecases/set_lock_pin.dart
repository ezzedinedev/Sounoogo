import '../repositories/app_lock_repository.dart';

/// Code de 4 à 6 chiffres. Les suites trop évidentes sont refusées.
final class SetLockPin {
  const SetLockPin(this._repository);

  static final _format = RegExp(r'^\d{4,6}$');
  static const _obvious = {'0000', '1111', '1234', '12345', '123456', '000000', '4321', '2580'};

  final AppLockRepository _repository;

  /// Renvoie un message d'erreur, ou `null` si le code est enregistré.
  Future<String?> call(String pin, {required String confirmation}) async {
    if (!_format.hasMatch(pin)) {
      return 'Le code doit contenir 4 à 6 chiffres.';
    }
    if (_obvious.contains(pin) || RegExp(r'^(\d)\1+$').hasMatch(pin)) {
      return 'Ce code est trop facile à deviner. Choisis-en un autre.';
    }
    if (pin != confirmation) {
      return 'Les deux codes ne correspondent pas.';
    }
    await _repository.savePin(pin);
    return null;
  }
}
