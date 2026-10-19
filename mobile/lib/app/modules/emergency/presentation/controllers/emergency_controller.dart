import 'package:get/get.dart';

import '../../../../shared/domain/entities/emergency_contact.dart';
import '../../../../shared/presentation/session_service.dart';
import '../../domain/usecases/alert_supervisor.dart';

class EmergencyController extends GetxController {
  EmergencyController(this._alertSupervisor);

  final AlertSupervisor _alertSupervisor;
  final SessionService _session = Get.find();

  RxList<EmergencyContact> get contacts => _session.emergencyContacts;

  /// Seul un membre connecté peut déclencher l'alerte superviseur.
  bool get canAlertSupervisor => _session.isSignedIn;

  /// Prévient un superviseur sans jamais bloquer l'affichage des numéros.
  void alertSupervisor() {
    if (canAlertSupervisor) {
      _alertSupervisor().ignore();
    }
  }
}
