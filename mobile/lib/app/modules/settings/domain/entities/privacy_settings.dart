/// Délai d'absence au-delà duquel l'app se verrouille.
enum LockDelay {
  immediate(Duration.zero, 'Immédiatement'),
  oneMinute(Duration(minutes: 1), 'Après 1 minute'),
  fiveMinutes(Duration(minutes: 5), 'Après 5 minutes');

  const LockDelay(this.duration, this.label);

  final Duration duration;
  final String label;

  static LockDelay fromName(String? name) => values.asNameMap()[name] ?? immediate;
}

/// Réglages du mode discret.
class PrivacySettings {
  const PrivacySettings({
    this.lockEnabled = false,
    this.biometricEnabled = false,
    this.lockDelay = LockDelay.immediate,
    this.hideInRecents = false,
    this.quickExitEnabled = false,
  });

  /// Verrou par code (et, si activé, par empreinte ou visage).
  final bool lockEnabled;
  final bool biometricEnabled;
  final LockDelay lockDelay;

  /// Masque le contenu dans les apps récentes et bloque les captures d'écran.
  final bool hideInRecents;

  /// Bouton qui bascule instantanément sur une calculatrice. Exige un code.
  final bool quickExitEnabled;

  PrivacySettings copyWith({
    bool? lockEnabled,
    bool? biometricEnabled,
    LockDelay? lockDelay,
    bool? hideInRecents,
    bool? quickExitEnabled,
  }) => PrivacySettings(
    lockEnabled: lockEnabled ?? this.lockEnabled,
    biometricEnabled: biometricEnabled ?? this.biometricEnabled,
    lockDelay: lockDelay ?? this.lockDelay,
    hideInRecents: hideInRecents ?? this.hideInRecents,
    quickExitEnabled: quickExitEnabled ?? this.quickExitEnabled,
  );
}
