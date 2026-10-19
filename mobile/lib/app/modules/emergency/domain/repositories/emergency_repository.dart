abstract interface class EmergencyRepository {
  /// Prévient en temps réel un superviseur de garde.
  Future<void> alertSupervisor();
}
