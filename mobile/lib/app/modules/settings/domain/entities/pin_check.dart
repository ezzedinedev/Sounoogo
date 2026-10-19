/// Résultat de la vérification du code.
sealed class PinCheck {
  const PinCheck();
}

final class PinAccepted extends PinCheck {
  const PinAccepted();
}

final class PinRejected extends PinCheck {
  const PinRejected(this.attemptsBeforeLockout);

  /// Essais restants avant un blocage temporaire.
  final int attemptsBeforeLockout;
}

/// Trop d'essais : nouvelle tentative possible après [until].
final class PinLockedOut extends PinCheck {
  const PinLockedOut(this.until);

  final DateTime until;
}
