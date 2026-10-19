import '../entities/push_event.dart';
import '../entities/reminder_settings.dart';

abstract interface class NotificationRepository {
  /// Prépare les notifications locales (fuseau horaire, canal, notification de lancement).
  Future<void> start();

  Future<ReminderSettings> reminder();

  /// Enregistre et programme (ou annule) le rappel. Renvoie false si les notifications sont refusées.
  Future<bool> saveReminder(ReminderSettings settings);

  /// Inscrit l'appareil aux notifications push du compte connecté (sans effet si elles ne sont pas configurées).
  Future<void> registerDevice();

  Future<void> unregisterDevice();

  /// Notifications ouvertes par le membre (y compris celle qui a lancé l'app).
  Stream<PushTarget> openedTargets();
}
